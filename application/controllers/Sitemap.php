<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Generator sitemap.xml & robots.txt dinamis.
 *
 * Routing:
 *   $route['sitemap.xml'] = 'sitemap/index';
 *   $route['robots.txt']  = 'sitemap/robots';
 *
 * Sumber data:
 * - Halaman utama (dengan lastmod dari kolom updated_at/created_at bila ada).
 * - Image sitemap dari tabel packages & gallery yang aktif.
 * - Tabel tambahan yang didefinisikan di config seo.php (wajib punya `slug`).
 */
class Sitemap extends CI_Controller
{
	/** @var array isi $config['seo'] */
	protected $seo_config = array();

	/** @var string domain kanonik tanpa trailing slash */
	protected $base = '';

	public function __construct()
	{
		parent::__construct();

		$this->config->load('seo', TRUE);
		$conf = $this->config->item('seo');

		$this->seo_config = is_array($conf) ? $conf : array();
		$this->base       = rtrim(isset($this->seo_config['canonical_base']) ? $this->seo_config['canonical_base'] : base_url(), '/');

		$this->load->helper('seo');
	}

	/**
	 * GET /sitemap.xml
	 *
	 * @return void
	 */
	public function index()
	{
		$urls = array($this->home_entry());

		foreach ($this->table_entries() as $entry)
		{
			$urls[] = $entry;
		}

		$xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";

		foreach ($urls as $url)
		{
			$xml .= "\t".'<url>'."\n";
			$xml .= "\t\t".'<loc>'.$this->esc($url['loc']).'</loc>'."\n";

			if ( ! empty($url['lastmod']))
			{
				$xml .= "\t\t".'<lastmod>'.$this->esc($url['lastmod']).'</lastmod>'."\n";
			}

			if ( ! empty($url['changefreq']))
			{
				$xml .= "\t\t".'<changefreq>'.$this->esc($url['changefreq']).'</changefreq>'."\n";
			}

			if ( ! empty($url['priority']))
			{
				$xml .= "\t\t".'<priority>'.$this->esc($url['priority']).'</priority>'."\n";
			}

			foreach (isset($url['images']) ? $url['images'] : array() as $image)
			{
				$xml .= "\t\t".'<image:image>'."\n";
				$xml .= "\t\t\t".'<image:loc>'.$this->esc($image['loc']).'</image:loc>'."\n";

				if ( ! empty($image['title']))
				{
					$xml .= "\t\t\t".'<image:title>'.$this->esc($image['title']).'</image:title>'."\n";
				}

				if ( ! empty($image['caption']))
				{
					$xml .= "\t\t\t".'<image:caption>'.$this->esc($image['caption']).'</image:caption>'."\n";
				}

				$xml .= "\t\t".'</image:image>'."\n";
			}

			$xml .= "\t".'</url>'."\n";
		}

		$xml .= '</urlset>'."\n";

		$cache = isset($this->seo_config['sitemap']['cache_minutes']) ? (int) $this->seo_config['sitemap']['cache_minutes'] : 0;

		$this->output
			->set_content_type('application/xml', 'utf-8')
			->set_output($xml);

		if ($cache > 0)
		{
			$this->output->cache($cache);
		}
	}

	/**
	 * GET /robots.txt (dipakai bila file statis tidak tersedia).
	 *
	 * @return void
	 */
	public function robots()
	{
		$rules = isset($this->seo_config['robots']) ? $this->seo_config['robots'] : array();
		$lines = array('User-agent: *');

		foreach (isset($rules['disallow']) ? $rules['disallow'] : array() as $path)
		{
			$lines[] = 'Disallow: '.$path;
		}

		foreach (isset($rules['allow']) ? $rules['allow'] : array() as $path)
		{
			$lines[] = 'Allow: '.$path;
		}

		$lines[] = '';
		$lines[] = 'Sitemap: '.$this->base.'/sitemap.xml';

		$this->output
			->set_content_type('text/plain', 'utf-8')
			->set_output(implode("\n", $lines)."\n");
	}

	// ------------------------------------------------------------------
	// Builder
	// ------------------------------------------------------------------

	/**
	 * Entri halaman utama beserta image sitemap.
	 *
	 * @return array
	 */
	protected function home_entry()
	{
		return array(
			'loc'        => $this->base.'/',
			'lastmod'    => $this->latest_change(),
			'changefreq' => 'daily',
			'priority'   => '1.0',
			'images'     => $this->home_images(),
		);
	}

	/**
	 * Entri dari tabel tambahan (config seo.php → sitemap.sources).
	 *
	 * @return array
	 */
	protected function table_entries()
	{
		$sources = isset($this->seo_config['sitemap']['sources']) ? $this->seo_config['sitemap']['sources'] : array();
		$entries = array();

		foreach ($sources as $source)
		{
			if (empty($source['table']) || ! $this->db->table_exists($source['table']))
			{
				continue;
			}

			$fields = $this->db->list_fields($source['table']);

			if ( ! in_array('slug', $fields, TRUE))
			{
				continue;
			}

			$prefix = isset($source['prefix']) ? trim($source['prefix'], '/').'/' : '';
			$rows   = $this->db->get($source['table'])->result_array();

			foreach ($rows as $row)
			{
				if (empty($row['slug']) || (isset($row['is_active']) && ! $row['is_active']))
				{
					continue;
				}

				$lastmod = '';

				foreach (array('updated_at', 'created_at') as $column)
				{
					if (in_array($column, $fields, TRUE) && ! empty($row[$column]))
					{
						$lastmod = date('c', strtotime($row[$column]));
						break;
					}
				}

				$entries[] = array(
					'loc'        => $this->base.'/'.$prefix.$row['slug'],
					'lastmod'    => $lastmod,
					'changefreq' => isset($source['changefreq']) ? $source['changefreq'] : 'weekly',
					'priority'   => isset($source['priority']) ? $source['priority'] : '0.7',
				);
			}
		}

		return $entries;
	}

	/**
	 * Gambar paket + galeri aktif (image sitemap).
	 *
	 * @return array
	 */
	protected function home_images()
	{
		$limit  = isset($this->seo_config['sitemap']['image_limit']) ? (int) $this->seo_config['sitemap']['image_limit'] : 20;
		$images = array();

		if ($limit < 1)
		{
			return $images;
		}

		$sources = array(
			'packages' => array('image' => 'image', 'title' => 'title_en', 'caption' => 'description_en'),
			'gallery'  => array('image' => 'image', 'title' => 'title_en', 'caption' => 'description_en'),
		);

		foreach ($sources as $table => $map)
		{
			if (count($images) >= $limit || ! $this->db->table_exists($table))
			{
				continue;
			}

			$rows = $this->db->where('is_active', 1)
				->order_by('sort_order', 'ASC')
				->limit($limit - count($images))
				->get($table)
				->result_array();

			foreach ($rows as $row)
			{
				$loc = $this->absolute(webp_url(isset($row[$map['image']]) ? $row[$map['image']] : ''));

				if ($loc === '')
				{
					continue;
				}

				$images[] = array(
					'loc'     => $loc,
					'title'   => isset($row[$map['title']]) ? teaser($row[$map['title']], 100) : '',
					'caption' => isset($row[$map['caption']]) ? teaser($row[$map['caption']], 150) : '',
				);
			}
		}

		return $images;
	}

	/**
	 * Tanggal perubahan konten terakhir (kolom updated_at/created_at bila ada),
	 * fallback ke tanggal hari ini.
	 *
	 * @return string format ISO 8601
	 */
	protected function latest_change()
	{
		$stamp = 0;

		foreach (array('packages', 'gallery', 'testimonials', 'faqs', 'why_points', 'settings') as $table)
		{
			if ( ! $this->db->table_exists($table))
			{
				continue;
			}

			$fields = $this->db->list_fields($table);

			foreach (array('updated_at', 'created_at') as $column)
			{
				if ( ! in_array($column, $fields, TRUE))
				{
					continue;
				}

				$row = $this->db->select_max($column, 'stamp')->get($table)->row_array();

				if ( ! empty($row['stamp']))
				{
					$stamp = max($stamp, (int) strtotime($row['stamp']));
				}

				break;
			}
		}

		return date('c', $stamp > 0 ? $stamp : time());
	}

	/**
	 * Pastikan URL absolut ke domain kanonik (bukan base_url() dev).
	 *
	 * @param string $url
	 * @return string
	 */
	protected function absolute($url)
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

		return $this->base.'/'.ltrim($url, '/');
	}

	/**
	 * Escape nilai untuk XML.
	 *
	 * @param string $value
	 * @return string
	 */
	protected function esc($value)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8');
	}
}