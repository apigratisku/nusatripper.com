<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Landing page publik.
 * Seluruh konten diambil dari database agar dapat diubah melalui admin.
 */
class Home extends MY_Controller
{
	/**
	 * Halaman utama sekaligus endpoint pencarian paket (?q=...).
	 * Dipakai sebagai target Sitelinks Searchbox.
	 *
	 * @return void
	 */
	public function index()
	{
		if ( ! $this->is_installed())
		{
			redirect('setup');
		}

		$this->load->model(array(
			'package_model',
			'gallery_model',
			'why_model',
			'testimonial_model',
			'faq_model',
		));

		$search   = trim((string) $this->input->get('q', TRUE));
		$packages = $this->package_model->active();

		if ($search !== '')
		{
			$packages = $this->filter_packages($packages, $search);
		}

		$gallery      = $this->gallery_model->active();
		$why_points   = $this->why_model->active();
		$testimonials = $this->testimonial_model->active();
		$faqs         = $this->faq_model->active();

		$this->build_seo($search, $packages, $faqs);

		// Halaman penuh di-cache; hasil pencarian tidak (URL berquery).
		if ($search === '')
		{
			$this->output->cache(120);
		}

		$this->load->view('home/index', array(
			'packages'     => $packages,
			'gallery'      => $gallery,
			'why_points'   => $why_points,
			'testimonials' => $testimonials,
			'faqs'         => $faqs,
			'search_query' => $search,
			'seo'          => $this->seo,
		));
	}

	/**
	 * Susun metadata + JSON-LD halaman utama.
	 *
	 * @param string $search
	 * @param array  $packages
	 * @param array  $faqs
	 * @return void
	 */
	private function build_seo($search, array $packages, array $faqs)
	{
		$this->load->library('seo_lib', NULL, 'seo');
		$this->seo->defaults($this->site);

		if ($search !== '')
		{
			$this->seo
				->page(array(
					'title'       => 'Hasil pencarian: '.$search.' | '.$this->seo->site_name(),
					'description' => 'Paket wisata dan trip yang cocok dengan pencarian "'.$search.'" di '.$this->seo->site_name().'.',
					'canonical'   => $this->seo->base().'/',
				))
				->noindex();

			return;
		}

		$review_count = isset($this->site['testi_review_count']['en'])
			? preg_replace('/[^0-9]/', '', (string) $this->site['testi_review_count']['en'])
			: 0;

		$this->seo
			->page(array('canonical' => $this->seo->base().'/'))
			->organization()
			->website()
			->breadcrumb(array(array('name' => 'Home', 'url' => '/')))
			->faq($faqs);

		$this->product_list($packages, (int) $review_count);
	}

	/**
	 * Ubah baris paket menjadi data Product JSON-LD.
	 *
	 * @param array $packages
	 * @param int   $review_count
	 * @return void
	 */
	private function product_list(array $packages, $review_count)
	{
		foreach ($packages as $package)
		{
			$this->seo->product(array(
				'id'           => $package['id'],
				'name'         => $package['title_en'],
				'description'  => $package['description_en'],
				'image'        => $package['image'],
				'url'          => '/#package-'.$package['id'],
				'price'        => $package['price'],
				'category'     => $package['category'],
				'rating'       => $package['rating'],
				'review_count' => $review_count,
			));
		}
	}

	/**
	 * Filter paket berdasarkan kata kunci (judul, deskripsi, kategori).
	 *
	 * @param array  $packages
	 * @param string $search
	 * @return array
	 */
	private function filter_packages(array $packages, $search)
	{
		$needle = strtolower($search);

		return array_values(array_filter($packages, function ($row) use ($needle) {
			$haystack = strtolower(
				(isset($row['title_en']) ? $row['title_en'] : '').' '.
				(isset($row['title_id']) ? $row['title_id'] : '').' '.
				(isset($row['description_en']) ? $row['description_en'] : '').' '.
				(isset($row['description_id']) ? $row['description_id'] : '').' '.
				(isset($row['category']) ? $row['category'] : '')
			);

			return strpos($haystack, $needle) !== FALSE;
		}));
	}
}