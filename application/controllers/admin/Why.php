<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRUD poin keunggulan (why choose us).
 */
class Why extends Admin_Resource
{
	protected $model_name = 'why_model';
	protected $title      = 'Keunggulan';
	protected $singular   = 'Keunggulan';
	protected $route      = 'admin/why';

	protected function fields()
	{
		return array(
			array(
				'name'  => 'title',
				'label' => 'Judul',
				'type'  => 'bilingual_text',
			),
			array(
				'name'  => 'icon',
				'label' => 'Ikon FontAwesome',
				'type'  => 'icon',
				'hint'  => 'Contoh: fa-solid fa-user-shield',
				'width' => 'half',
			),
			array(
				'name'  => 'description',
				'label' => 'Deskripsi',
				'type'  => 'bilingual_textarea',
			),
		);
	}

	protected function list_columns()
	{
		return array(
			array('name' => 'title', 'label' => 'Judul', 'type' => 'bilingual'),
			array('name' => 'icon', 'label' => 'Ikon', 'type' => 'text'),
			array('name' => 'sort_order', 'label' => 'Urutan', 'type' => 'order'),
			array('name' => 'is_active', 'label' => 'Status', 'type' => 'status'),
		);
	}
}
