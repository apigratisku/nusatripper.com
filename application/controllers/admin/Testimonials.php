<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRUD testimoni pelanggan.
 */
class Testimonials extends Admin_Resource
{
	protected $model_name = 'testimonial_model';
	protected $title      = 'Testimoni';
	protected $singular   = 'Testimoni';
	protected $route      = 'admin/testimonials';

	protected function fields()
	{
		return array(
			array(
				'name'  => 'name',
				'label' => 'Nama Pelanggan',
				'type'  => 'text',
				'width' => 'half',
			),
			array(
				'name'  => 'initials',
				'label' => 'Inisial (avatar)',
				'type'  => 'text',
				'hint'  => 'Maksimal 2-3 huruf, contoh: ML',
				'width' => 'half',
			),
			array(
				'name'  => 'origin',
				'label' => 'Asal / Kota',
				'type'  => 'bilingual_text',
				'width' => 'half',
			),
			array(
				'name'    => 'rating',
				'label'   => 'Rating',
				'type'    => 'rating',
				'default' => 5,
				'width'   => 'half',
			),
			array(
				'name'  => 'quote',
				'label' => 'Isi Testimoni',
				'type'  => 'bilingual_textarea',
			),
		);
	}

	protected function list_columns()
	{
		return array(
			array('name' => 'name', 'label' => 'Nama', 'type' => 'text'),
			array('name' => 'origin', 'label' => 'Asal', 'type' => 'bilingual'),
			array('name' => 'rating', 'label' => 'Rating', 'type' => 'text'),
			array('name' => 'sort_order', 'label' => 'Urutan', 'type' => 'order'),
			array('name' => 'is_active', 'label' => 'Status', 'type' => 'status'),
		);
	}
}
