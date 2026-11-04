<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRUD item galeri (foto / video).
 */
class Gallery extends Admin_Resource
{
	protected $model_name = 'gallery_model';
	protected $title      = 'Galeri';
	protected $singular   = 'Item Galeri';
	protected $route      = 'admin/gallery';

	/**
	 * Daftar paket untuk dropdown pemilik galeri.
	 *
	 * @return array package_id => nama paket
	 */
	protected function package_options()
	{
		$this->load->model('package_model');

		$options = array(0 => '— Galeri Umum (landing page) —');

		foreach ($this->package_model->all() as $package)
		{
			$label = isset($package['title_en']) ? trim((string) $package['title_en']) : '';

			if ($label === '')
			{
				$label = 'Paket #'.(int) $package['id'];
			}

			$options[(int) $package['id']] = $label;
		}

		return $options;
	}

	protected function fields()
	{
		return array(
			array(
				'name'    => 'package_id',
				'label'   => 'Paket Terkait',
				'type'    => 'select_int',
				'options' => $this->package_options(),
				'default' => 0,
				'width'   => 'full',
				'hint'    => 'Pilih "Galeri Umum" untuk section Galeri di landing page, atau pilih paket agar foto/video tampil saat gambar paket diklik.',
			),
			array(
				'name'    => 'media_type',
				'label'   => 'Tipe Media',
				'type'    => 'select',
				'default' => 'image',
				'options' => array(
					'image' => 'Foto',
					'video' => 'Video',
				),
				'width'   => 'half',
			),
			array(
				'name'    => 'size',
				'label'   => 'Ukuran Kartu',
				'type'    => 'select',
				'default' => 'normal',
				'options' => array(
					'normal' => 'Normal',
					'large'  => 'Besar (2 kolom)',
				),
				'width'   => 'half',
			),
			array(
				'name'  => 'title',
				'label' => 'Judul',
				'type'  => 'bilingual_text',
			),
			array(
				'name'  => 'category',
				'label' => 'Kategori / Lokasi',
				'type'  => 'bilingual_text',
			),
			array(
				'name'  => 'description',
				'label' => 'Deskripsi',
				'type'  => 'bilingual_textarea',
			),
			array(
				'name'  => 'image',
				'label' => 'Foto (wajib, juga menjadi poster untuk video)',
				'type'  => 'image',
			),
			array(
				'name'  => 'video',
				'label' => 'Video (opsional, untuk tipe video)',
				'type'  => 'video',
			),
		);
	}

	protected function list_columns()
	{
		return array(
			array('name' => 'image', 'label' => 'Media', 'type' => 'image'),
			array('name' => 'title', 'label' => 'Judul', 'type' => 'bilingual'),
			array('name' => 'package_id', 'label' => 'Paket', 'type' => 'package'),
			array('name' => 'media_type', 'label' => 'Tipe', 'type' => 'text'),
			array('name' => 'size', 'label' => 'Ukuran', 'type' => 'text'),
			array('name' => 'sort_order', 'label' => 'Urutan', 'type' => 'order'),
			array('name' => 'is_active', 'label' => 'Status', 'type' => 'status'),
		);
	}
}
