<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Landing page publik.
 * Seluruh konten diambil dari database agar dapat diubah melalui admin.
 */
class Home extends MY_Controller
{
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

		$this->load->view('home/index', array(
			'packages'     => $this->package_model->active(),
			'gallery'      => $this->gallery_model->active(),
			'why_points'   => $this->why_model->active(),
			'testimonials' => $this->testimonial_model->active(),
			'faqs'         => $this->faq_model->active(),
		));
	}
}
