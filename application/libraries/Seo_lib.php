<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Generator metadata SEO + JSON-LD (Schema.org).
 *
 * Pemakaian di controller:
 *   $this->load->library('seo_lib', NULL, 'seo');
 *   $this->seo->defaults($this->site);
 *   $this->seo->page(array('title' => 'Judul unik', 'description' => '...'));
 *   $this->seo->organization()->website()->breadcrumb(array(...));
 *   $this->seo->product(array(...));
 *
 * Pemakaian di view:
 *   echo $seo->render_meta();
 *   echo $seo->render_schema();
 *
 * Semua URL kanonik dipaksa ke https://nusatripper.com (non-www, tanpa query).
 */
class Seo_lib
{
	/** @var CI_Controller */
	protected $CI;

	/** @var array isi $config['seo'] */
	protected $config = array();

	/** @var array data brand/organisasi */
	protected $brand = array();

	/** @var array metadata halaman */
	protected $meta = array();

	/** @var array node JSON-LD */
	protected $nodes = array();

	/** @var array hreflang => URL */
	protected $alternates = array();

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->config->load('seo', TRUE);
		$conf = $this->CI->config->item('seo');

		$this->config = is_array($conf) ? $conf : array();
		$this->brand  = isset($this->config['brand']) ? $this->config['brand'] : array();

		$this->CI->load->helper(array('url', 'i18n'));

		$this->meta = array(
			'title'        => '',
			'description'  => '',
			'keywords'     => '',
			'canonical'    => '',
			'robots'       => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
			'type'         => 'website',
			'image'        => '',
			'image_width'  => 1200,
			'image_height' => 630,
			'image_alt'    => '',
			'author'       => '',
			'published'    => '',
			'modified'     => '',
			'site_name'    => $this->brand_value('name'),
			'locale'       => isset($this->config['locale']) ? $this->config['locale'] : 'en_US',
		);
	}

	// ------------------------------------------------------------------
	// Pengisian data
	// ------------------------------------------------------------------

	/**
	 * Isi metadata dari setting situs (tabel settings) sebagai default.
	 * Nilai yang tidak diisi admin otomatis memakai template brand.
	 *
	 * @param array $site key => ['en' => ..., 'id' => ...]
	 * @return $this
	 */
	public function defaults(array $site = array())
	{
		$get = function ($key) use ($site) {
			if ( ! isset($site[$key]) || ! is_array($site[$key]))
			{
				return '';
			}

			$value = isset($site[$key]['en']) ? trim((string) $site[$key]['en']) : '';

			if ($value === '' && isset($site[$key]['id']))
			{
				$value = trim((string) $site[$key]['id']);
			}

			return $value;
		};

		$name    = $get('site_name') !== '' ? $get('site_name') : $this->brand_value('name');
		$tagline = $get('brand_subtitle');

		$this->meta['site_name']   = $name;
		$this->meta['title']       = $get('meta_title') !== '' ? $get('meta_title') : $name;
		$this->meta['description'] = $get('meta_description');
		$this->meta['keywords']    = $get('meta_keywords');
		$this->meta['image']       = $this->absolute(media_url($get('og_image')));
		$this->meta['image_alt']   = $name;

		// Fallback cerdas bila admin belum mengisi meta description.
		if ($this->meta['description'] === '')
		{
			$this->meta['description'] = $name.($tagline !== '' ? ' — '.$tagline : ' — tour & travel di Lombok');
		}

		$this->brand['name']         = $name;
		$this->brand['phone']        = $get('phone_display') !== '' ? $get('phone_display') : $this->brand_value('phone');
		$this->brand['location']     = $get('location');
		$this->brand['rating']       = $get('testi_rating');
		$this->brand['review_count'] = $get('testi_review_count');
		$this->brand['same_as']      = array_values(array_filter(array(
			$get('social_instagram'),
			$get('social_facebook'),
			$get('social_tripadvisor'),
		)));

		return $this;
	}

	/**
	 * Set metadata halaman (menimpa default).
	 *
	 * Kunci yang didukung: title, description, keywords, canonical, robots,
	 * type, image, image_width, image_height, image_alt, author,
	 * published, modified.
	 *
	 * @param array $options
	 * @return $this
	 */
	public function page(array $options)
	{
		foreach (array('title', 'description', 'keywords', 'robots', 'type', 'author', 'published', 'modified', 'image_alt') as $key)
		{
			if (isset($options[$key]))
			{
				$this->meta[$key] = (string) $options[$key];
			}
		}

		if (isset($options['image']))
		{
			$this->meta['image'] = $this->absolute($options['image']);
		}

		if (isset($options['image_width']))
		{
			$this->meta['image_width'] = (int) $options['image_width'];
		}

		if (isset($options['image_height']))
		{
			$this->meta['image_height'] = (int) $options['image_height'];
		}

		if (isset($options['canonical']))
		{
			$this->meta['canonical'] = $this->absolute($options['canonical']);
		}

		if ($this->meta['canonical'] === '')
		{
			$this->meta['canonical'] = $this->canonical_url();
		}

		if ($this->meta['image'] === '')
		{
			$this->meta['image'] = $this->absolute($this->brand_value('logo'));
		}

		return $this;
	}

	/**
	 * Tandai halaman tidak boleh diindeks (mis. hasil pencarian internal).
	 *
	 * @return $this
	 */
	public function noindex()
	{
		$this->meta['robots'] = 'noindex, follow';

		return $this;
	}

	/**
	 * Daftar alternate hreflang. Hanya dipakai bila situs benar-benar
	 * memiliki URL terpisah per bahasa (mis. /en/, /id/).
	 *
	 * @param array $map lang => URL
	 * @return $this
	 */
	public function alternates(array $map)
	{
		$this->alternates = $map;

		return $this;
	}

	// ------------------------------------------------------------------
	// Schema.org
	// ------------------------------------------------------------------

	/**
	 * Organization + TravelAgency.
	 *
	 * @return $this
	 */
	public function organization()
	{
		$id = $this->base().'/#organization';

		if ($this->node_exists($id))
		{
			return $this;
		}

		$node = array(
			'@type'       => array('Organization', 'TravelAgency'),
			'@id'         => $id,
			'name'        => $this->brand['name'],
			'legalName'   => $this->brand_value('legal') !== '' ? $this->brand_value('legal') : $this->brand['name'],
			'url'         => $this->base().'/',
			'description' => $this->meta['description'],
			'telephone'   => $this->brand['phone'],
			'email'       => $this->brand_value('email'),
			'priceRange'  => '$$',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $this->brand_value('street'),
				'addressLocality' => $this->brand_value('city'),
				'addressRegion'   => $this->brand_value('region'),
				'postalCode'      => $this->brand_value('postal'),
				'addressCountry'  => $this->brand_value('country'),
			),
			'geo' => array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => $this->brand_value('latitude'),
				'longitude' => $this->brand_value('longitude'),
			),
			'areaServed' => $this->brand['location'] !== '' ? $this->brand['location'] : $this->brand_value('city'),
			'sameAs'     => $this->brand['same_as'],
		);

		if ($this->meta['image'] !== '')
		{
			$node['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $this->absolute($this->brand_value('logo')),
			);
			$node['image'] = $this->meta['image'];
		}

		$rating = isset($this->brand['rating']) ? (float) $this->brand['rating'] : 0;
		$count  = isset($this->brand['review_count']) ? (int) preg_replace('/[^0-9]/', '', (string) $this->brand['review_count']) : 0;

		if ($rating > 0 && $count > 0)
		{
			$node['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => $rating,
				'reviewCount' => $count,
				'bestRating'  => 5,
			);
		}

		$this->nodes[] = $this->clean($node);

		return $this;
	}

	/**
	 * WebSite + Sitelinks Searchbox.
	 *
	 * @return $this
	 */
	public function website()
	{
		$id = $this->base().'/#website';

		if ($this->node_exists($id))
		{
			return $this;
		}

		$node = array(
			'@type'     => 'WebSite',
			'@id'       => $id,
			'url'       => $this->base().'/',
			'name'      => $this->meta['site_name'],
			'publisher' => array('@id' => $this->base().'/#organization'),
			'inLanguage'=> 'en',
		);

		$search = isset($this->config['search_url']) ? trim((string) $this->config['search_url']) : '';

		if ($search !== '')
		{
			$node['potentialAction'] = array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => $search,
				),
				'query-input' => 'required name=search_term_string',
			);
		}

		$this->nodes[] = $node;

		return $this;
	}

	/**
	 * BreadcrumbList.
	 *
	 * @param array $items [['name' => 'Home', 'url' => '/'], ...]
	 * @return $this
	 */
	public function breadcrumb(array $items)
	{
		if (empty($items))
		{
			return $this;
		}

		$list = array();
		$pos  = 1;

		foreach ($items as $item)
		{
			if ( ! isset($item['name']) || trim((string) $item['name']) === '')
			{
				continue;
			}

			$list[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => (string) $item['name'],
				'item'     => $this->absolute(isset($item['url']) ? $item['url'] : '/'),
			);
		}

		if ($list)
		{
			$this->nodes[] = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $this->base().'/#breadcrumb',
				'itemListElement' => $list,
			);
		}

		return $this;
	}

	/**
	 * Kumpulan Product dalam ItemList (halaman daftar paket).
	 *
	 * @param array $packages
	 * @return $this
	 */
	public function products(array $packages)
	{
		foreach ($packages as $package)
		{
			$this->product($package);
		}

		return $this;
	}

	/**
	 * Product + TouristTrip untuk satu paket/trip.
	 *
	 * Kunci: id, name, description, image, url, price, category, rating,
	 *        review_count.
	 *
	 * @param array $data
	 * @return $this
	 */
	public function product(array $data)
	{
		$name = isset($data['name']) ? trim((string) $data['name']) : '';

		if ($name === '')
		{
			return $this;
		}

		$url = $this->absolute(isset($data['url']) ? $data['url'] : '');
		$id  = $url !== '' ? $url.'#product' : $this->base().'/#product-'.(isset($data['id']) ? (int) $data['id'] : 0);

		$node = array(
			'@type'          => 'Product',
			'@id'            => $id,
			'name'           => $name,
			'description'    => isset($data['description']) ? teaser($data['description'], 300) : '',
			'url'            => $url,
			'additionalType' => 'https://schema.org/TouristTrip',
			'category'       => isset($data['category']) ? (string) $data['category'] : 'Tour',
			'brand'          => array(
				'@type' => 'Brand',
				'name'  => $this->brand['name'],
			),
			'provider' => array('@id' => $this->base().'/#organization'),
		);

		if ( ! empty($data['image']))
		{
			$node['image'] = $this->absolute($data['image']);
		}

		$price = isset($data['price']) ? preg_replace('/[^0-9]/', '', (string) $data['price']) : '';

		if ($price !== '')
		{
			$node['offers'] = array(
				'@type'         => 'Offer',
				'url'           => $url,
				'price'         => (float) $price,
				'priceCurrency' => 'IDR',
				'availability'  => 'https://schema.org/InStock',
				'seller'        => array('@id' => $this->base().'/#organization'),
			);
		}

		$rating = isset($data['rating']) ? (float) $data['rating'] : 0;
		$count  = isset($data['review_count']) ? (int) $data['review_count'] : 0;

		if ($rating > 0 && $count > 0)
		{
			$node['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => $rating,
				'reviewCount' => $count,
				'bestRating'  => 5,
			);
		}

		$this->nodes[] = $this->clean($node);

		return $this;
	}

	/**
	 * Article / BlogPosting.
	 *
	 * Kunci: url, headline, description, image, author, published, modified.
	 *
	 * @param array $data
	 * @return $this
	 */
	public function article(array $data)
	{
		$url      = $this->absolute(isset($data['url']) ? $data['url'] : '');
		$headline = isset($data['headline']) ? trim((string) $data['headline']) : '';

		if ($headline === '')
		{
			return $this;
		}

		$node = array(
			'@type'            => isset($data['type']) ? (string) $data['type'] : 'BlogPosting',
			'@id'              => $url.'#article',
			'headline'         => teaser($headline, 110),
			'description'      => isset($data['description']) ? teaser($data['description'], 300) : '',
			'datePublished'    => isset($data['published']) ? (string) $data['published'] : '',
			'dateModified'     => isset($data['modified']) ? (string) $data['modified'] : (isset($data['published']) ? (string) $data['published'] : ''),
			'author'           => array(
				'@type' => 'Person',
				'name'  => isset($data['author']) ? (string) $data['author'] : $this->brand['name'],
			),
			'publisher'        => array('@id' => $this->base().'/#organization'),
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => $url,
			),
			'inLanguage'       => 'en',
		);

		if ( ! empty($data['image']))
		{
			$node['image'] = array($this->absolute($data['image']));
		}

		$this->nodes[] = $this->clean($node);

		return $this;
	}

	/**
	 * FAQPage dari baris tabel `faqs`.
	 *
	 * @param array $rows
	 * @return $this
	 */
	public function faq(array $rows)
	{
		$items = array();

		foreach ($rows as $row)
		{
			$question = isset($row['question_en']) ? trim(strip_tags((string) $row['question_en'])) : '';
			$answer   = isset($row['answer_en']) ? trim(strip_tags((string) $row['answer_en'])) : '';

			if ($question === '' || $answer === '')
			{
				continue;
			}

			$items[] = array(
				'@type'          => 'Question',
				'name'           => $question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}

		if ($items)
		{
			$this->nodes[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $this->base().'/#faq',
				'mainEntity' => $items,
			);
		}

		return $this;
	}

	// ------------------------------------------------------------------
	// Output
	// ------------------------------------------------------------------

	/**
	 * Seluruh tag <head> SEO.
	 *
	 * @return string
	 */
	public function render_meta()
	{
		$e     = function ($value) {
			return html_escape((string) $value);
		};
		$lines = array();

		$lines[] = '<title>'.$e($this->meta['title']).'</title>';

		if ($this->meta['description'] !== '')
		{
			$lines[] = '<meta name="description" content="'.$e($this->meta['description']).'">';
		}

		if ($this->meta['keywords'] !== '')
		{
			$lines[] = '<meta name="keywords" content="'.$e($this->meta['keywords']).'">';
		}

		if ($this->meta['author'] !== '')
		{
			$lines[] = '<meta name="author" content="'.$e($this->meta['author']).'">';
		}

		$lines[] = '<meta name="robots" content="'.$e($this->meta['robots']).'">';
		$lines[] = '<meta name="theme-color" content="#0A2540">';
		$lines[] = '<link rel="canonical" href="'.$e($this->meta['canonical']).'">';

		// Favicons & Web App Icons
		$lines[] = '<link rel="icon" type="image/svg+xml" href="'.$e($this->absolute('assets/images/favicon.svg')).'">';
		$lines[] = '<link rel="icon" type="image/png" sizes="32x32" href="'.$e($this->absolute('assets/images/favicon-32x32.png')).'">';
		$lines[] = '<link rel="icon" type="image/png" sizes="16x16" href="'.$e($this->absolute('assets/images/favicon-16x16.png')).'">';
		$lines[] = '<link rel="apple-touch-icon" sizes="180x180" href="'.$e($this->absolute('assets/images/apple-touch-icon.png')).'">';
		$lines[] = '<link rel="manifest" href="'.$e($this->absolute('site.webmanifest')).'">';

		foreach ($this->alternates as $lang => $href)
		{
			$lines[] = '<link rel="alternate" hreflang="'.$e($lang).'" href="'.$e($this->absolute($href)).'">';
		}

		// Open Graph (Facebook, WhatsApp, LinkedIn).
		$lines[] = '<meta property="og:site_name" content="'.$e($this->meta['site_name']).'">';
		$lines[] = '<meta property="og:locale" content="'.$e($this->meta['locale']).'">';
		$lines[] = '<meta property="og:type" content="'.$e($this->meta['type']).'">';
		$lines[] = '<meta property="og:title" content="'.$e($this->meta['title']).'">';
		$lines[] = '<meta property="og:description" content="'.$e($this->meta['description']).'">';
		$lines[] = '<meta property="og:url" content="'.$e($this->meta['canonical']).'">';

		if ($this->meta['image'] !== '')
		{
			$lines[] = '<meta property="og:image" content="'.$e($this->meta['image']).'">';
			$lines[] = '<meta property="og:image:secure_url" content="'.$e($this->meta['image']).'">';
			$lines[] = '<meta property="og:image:width" content="'.(int) $this->meta['image_width'].'">';
			$lines[] = '<meta property="og:image:height" content="'.(int) $this->meta['image_height'].'">';

			if ($this->meta['image_alt'] !== '')
			{
				$lines[] = '<meta property="og:image:alt" content="'.$e($this->meta['image_alt']).'">';
			}
		}

		if ($this->meta['type'] === 'article')
		{
			if ($this->meta['published'] !== '')
			{
				$lines[] = '<meta property="article:published_time" content="'.$e($this->meta['published']).'">';
			}

			if ($this->meta['modified'] !== '')
			{
				$lines[] = '<meta property="article:modified_time" content="'.$e($this->meta['modified']).'">';
			}
		}

		// Twitter / X Cards.
		$lines[] = '<meta name="twitter:card" content="summary_large_image">';
		$lines[] = '<meta name="twitter:title" content="'.$e($this->meta['title']).'">';
		$lines[] = '<meta name="twitter:description" content="'.$e($this->meta['description']).'">';

		if ($this->meta['image'] !== '')
		{
			$lines[] = '<meta name="twitter:image" content="'.$e($this->meta['image']).'">';
		}

		$twitter = isset($this->config['twitter_site']) ? trim((string) $this->config['twitter_site']) : '';

		if ($twitter !== '')
		{
			$lines[] = '<meta name="twitter:site" content="'.$e($twitter).'">';
		}

		return implode("\n\t", $lines);
	}

	/**
	 * Blok JSON-LD tunggal (@graph).
	 *
	 * @return string
	 */
	public function render_schema()
	{
		if (empty($this->nodes))
		{
			return '';
		}

		$graph = array(
			'@context' => 'https://schema.org',
			'@graph'   => $this->nodes,
		);

		$json = json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

		if ($json === FALSE)
		{
			return '';
		}

		// Cegah penutupan tag <script> bila ada karakter "</" di dalam data.
		$json = str_replace('</', '<\/', $json);

		return '<script type="application/ld+json">'."\n".$json."\n".'</script>';
	}

	// ------------------------------------------------------------------
	// Getter & utilitas
	// ------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function site_name()
	{
		return $this->meta['site_name'];
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public function get($key)
	{
		return isset($this->meta[$key]) ? $this->meta[$key] : '';
	}

	/**
	 * @return string
	 */
	public function canonical()
	{
		return $this->meta['canonical'];
	}

	/**
	 * Basis domain kanonik tanpa trailing slash.
	 *
	 * @return string
	 */
	public function base()
	{
		$base = isset($this->config['canonical_base']) ? trim((string) $this->config['canonical_base']) : base_url();

		return rtrim($base, '/');
	}

	/**
	 * Ubah URL relatif menjadi absolut terhadap domain kanonik.
	 *
	 * @param string $url
	 * @return string
	 */
	public function absolute($url)
	{
		$url = trim((string) $url);

		if ($url === '')
		{
			return '';
		}

		// Buang prefix base_url() (host dev) agar memakai domain kanonik.
		$dev = base_url();

		if (stripos($url, $dev) === 0)
		{
			$url = ltrim(substr($url, strlen($dev)), '/');
		}

		if (preg_match('#^https?://#i', $url) === 1)
		{
			return $url;
		}

		if (strpos($url, '//') === 0)
		{
			return 'https:'.$url;
		}

		return $this->base().'/'.ltrim($url, '/');
	}

	/**
	 * Canonical untuk URI saat ini (query string dibuang).
	 *
	 * @return string
	 */
	protected function canonical_url()
	{
		$uri = trim($this->CI->uri->uri_string(), '/');

		return $this->base().($uri === '' ? '/' : '/'.$uri);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function brand_value($key)
	{
		return (isset($this->brand[$key]) && is_scalar($this->brand[$key])) ? trim((string) $this->brand[$key]) : '';
	}

	/**
	 * @param string $id
	 * @return bool
	 */
	protected function node_exists($id)
	{
		foreach ($this->nodes as $node)
		{
			if (isset($node['@id']) && $node['@id'] === $id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Buang nilai kosong agar JSON-LD tetap valid & ringkas.
	 *
	 * @param array $node
	 * @return array
	 */
	protected function clean(array $node)
	{
		foreach ($node as $key => $value)
		{
			if (is_array($value))
			{
				$value = $this->clean($value);
			}

			if ($value === '' || $value === NULL || $value === array())
			{
				unset($node[$key]);
				continue;
			}

			$node[$key] = $value;
		}

		return $node;
	}
}
