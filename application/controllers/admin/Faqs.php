<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRUD pertanyaan umum (FAQ).
 */
class Faqs extends Admin_Resource
{
	protected $model_name = 'faq_model';
	protected $title      = 'FAQ';
	protected $singular   = 'FAQ';
	protected $route      = 'admin/faqs';

	protected function fields()
	{
		return array(
			array(
				'name'  => 'question',
				'label' => 'Pertanyaan',
				'type'  => 'bilingual_text',
			),
			array(
				'name'  => 'answer',
				'label' => 'Jawaban',
				'type'  => 'bilingual_textarea',
			),
		);
	}

	protected function list_columns()
	{
		return array(
			array('name' => 'question', 'label' => 'Pertanyaan', 'type' => 'bilingual'),
			array('name' => 'sort_order', 'label' => 'Urutan', 'type' => 'order'),
			array('name' => 'is_active', 'label' => 'Status', 'type' => 'status'),
		);
	}
}
