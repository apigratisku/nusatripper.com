<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ringkasan konten pada dashboard admin.
 */
class Dashboard extends Admin_Controller
{
	public function index()
	{
		$this->load->model(array(
			'package_model',
			'gallery_model',
			'why_model',
			'testimonial_model',
			'faq_model',
			'setting_model',
		));

		$stats = array(
			'packages'     => $this->package_model->count_all(),
			'packages_on'  => $this->package_model->count_active(),
			'gallery'      => $this->gallery_model->count_all(),
			'gallery_on'   => $this->gallery_model->count_active(),
			'why'          => $this->why_model->count_all(),
			'testimonials' => $this->testimonial_model->count_all(),
			'faqs'         => $this->faq_model->count_all(),
			'settings'     => count($this->setting_model->all_rows()),
		);

		$this->render('admin/dashboard', array(
			'page_title' => 'Dashboard',
			'stats'      => $stats,
		));
	}
}
