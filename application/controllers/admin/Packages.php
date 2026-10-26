<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRUD paket wisata.
 */
class Packages extends Admin_Resource
{
	protected $model_name = 'package_model';
	protected $title      = 'Paket Wisata';
	protected $singular   = 'Paket';
	protected $route      = 'admin/packages';

	protected function fields()
	{
		return array(
			array(
				'name'  => 'title',
				'label' => 'Judul Paket',
				'type'  => 'bilingual_text',
			),
			array(
				'name'    => 'category',
				'label'   => 'Kategori (untuk filter)',
				'type'    => 'select',
				'default' => 'diving',
				'options' => array(
					'diving'    => 'Diving & Snorkeling',
					'island'    => 'Island & Overland',
					'transport' => 'VIP Transport',
				),
				'width'   => 'half',
			),
			array(
				'name'    => 'badge_style',
				'label'   => 'Gaya Badge',
				'type'    => 'select',
				'default' => '',
				'options' => array(
					''        => 'Tanpa badge',
					'hot'     => 'Hot (merah)',
					'popular' => 'Popular (teal)',
					'new'     => 'New (gold)',
				),
				'width'   => 'half',
			),
			array(
				'name'  => 'badge',
				'label' => 'Teks Badge',
				'type'  => 'bilingual_text',
				'hint'  => 'Kosongkan bila paket tidak memakai badge.',
			),
			array(
				'name'  => 'description',
				'label' => 'Deskripsi Singkat',
				'type'  => 'bilingual_textarea',
			),
			array(
				'name'  => 'inclusions',
				'label' => 'Fasilitas / Include',
				'type'  => 'bilingual_textarea',
				'hint'  => 'Satu fasilitas per baris.',
			),
			array(
				'name'  => 'duration',
				'label' => 'Durasi',
				'type'  => 'bilingual_text',
				'width' => 'half',
			),
			array(
				'name'  => 'price_label',
				'label' => 'Label Harga',
				'type'  => 'bilingual_text',
				'hint'  => 'Contoh: Starting from / Mulai dari.',
				'width' => 'half',
			),
			array(
				'name'  => 'price',
				'label' => 'Harga',
				'type'  => 'text',
				'hint'  => 'Contoh: IDR 3.500.000',
				'width' => 'half',
			),
			array(
				'name'  => 'price_unit',
				'label' => 'Satuan Harga',
				'type'  => 'text',
				'hint'  => 'Contoh: /pax, /car, /route',
				'width' => 'half',
			),
			array(
				'name'  => 'rating',
				'label' => 'Rating',
				'type'  => 'text',
				'hint'  => 'Contoh: 4.9',
				'width' => 'half',
			),
			array(
				'name'  => 'wa_message',
				'label' => 'Pesan WhatsApp Otomatis',
				'type'  => 'textarea',
				'hint'  => 'Teks yang terisi saat pengunjung klik tombol pesan.',
				'width' => 'half',
			),
			array(
				'name'  => 'image',
				'label' => 'Foto Paket',
				'type'  => 'image',
			),
		);
	}

	protected function list_columns()
	{
		return array(
			array('name' => 'image', 'label' => 'Foto', 'type' => 'image'),
			array('name' => 'title', 'label' => 'Judul', 'type' => 'bilingual'),
			array('name' => 'price', 'label' => 'Harga', 'type' => 'text'),
			array('name' => 'sort_order', 'label' => 'Urutan', 'type' => 'order'),
			array('name' => 'is_active', 'label' => 'Status', 'type' => 'status'),
		);
	}
}
