<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pengaturan konten global (teks, media, SEO, kontak, navigasi).
 */
class Settings extends Admin_Controller
{
	/** @var array label grup pengaturan */
	protected $group_labels = array(
		'umum'      => 'Umum & SEO',
		'kontak'    => 'Kontak',
		'sosial'    => 'Sosial Media',
		'nav'       => 'Navigasi',
		'hero'      => 'Hero & Trust Badge',
		'featured'  => 'Ekspedisi Unggulan',
		'paket'     => 'Section Paket',
		'why'       => 'Section Keunggulan',
		'galeri'    => 'Section Galeri',
		'testimoni' => 'Section Testimoni',
		'faq'       => 'Section FAQ',
		'cta'       => 'Call to Action',
		'footer'    => 'Footer',
	);

	/** @var array ikon per grup */
	protected $group_icons = array(
		'umum'      => 'fa-solid fa-globe',
		'kontak'    => 'fa-solid fa-address-book',
		'sosial'    => 'fa-solid fa-share-nodes',
		'nav'       => 'fa-solid fa-bars',
		'hero'      => 'fa-solid fa-image',
		'featured'  => 'fa-solid fa-star',
		'paket'     => 'fa-solid fa-suitcase-rolling',
		'why'       => 'fa-solid fa-medal',
		'galeri'    => 'fa-solid fa-images',
		'testimoni' => 'fa-solid fa-quote-left',
		'faq'       => 'fa-solid fa-circle-question',
		'cta'       => 'fa-solid fa-bullhorn',
		'footer'    => 'fa-solid fa-shoe-prints',
	);

	public function index()
	{
		$this->load->model('setting_model');

		if ($this->input->method() === 'post')
		{
			$this->save();
		}

		$grouped = $this->setting_model->grouped();

		$this->render('admin/settings/index', array(
			'page_title'    => 'Pengaturan Situs',
			'grouped'       => $grouped,
			'group_labels'  => $this->group_labels,
			'group_icons'   => $this->group_icons,
		));
	}

	/**
	 * Simpan semua nilai setting.
	 *
	 * @return void
	 */
	protected function save()
	{
		$rows = $this->setting_model->all_rows();

		$en = $this->input->post('value_en');
		$id = $this->input->post('value_id');
		$en = is_array($en) ? $en : array();
		$id = is_array($id) ? $id : array();

		$values = array();
		$errors = array();

		foreach ($rows as $row)
		{
			$key  = $row['setting_key'];
			$type = $row['field_type'];

			if ($type === 'image' || $type === 'video')
			{
				$current = (string) $row['value_en'];

				if ($this->post_flag($key.'_remove'))
				{
					$this->delete_media($current);
					$current = '';
				}

				$upload = $this->do_upload(
					$key.'_file',
					$type === 'video' ? 'mp4|webm|ogg|mov' : 'jpg|jpeg|png|webp|gif'
				);

				if ($this->upload_error !== '')
				{
					$errors[] = $row['label'].': '.$this->upload_error;
				}

				if ($upload !== NULL)
				{
					$this->delete_media($current);
					$current = $upload;
				}

				$values[$key] = array('en' => $current, 'id' => (string) $row['value_id']);
			}
			else
			{
				// Abaikan field yang tidak ikut terkirim agar tidak menimpa nilai lama.
				if ( ! array_key_exists($key, $en) && ! array_key_exists($key, $id))
				{
					continue;
				}

				$values[$key] = array(
					'en' => isset($en[$key]) ? trim((string) $en[$key]) : '',
					'id' => isset($id[$key]) ? trim((string) $id[$key]) : '',
				);
			}
		}

		$this->setting_model->update_many($values);

		if ( ! empty($errors))
		{
			$this->flash('error', 'Sebagian media gagal diunggah. '.implode(' ', $errors));
		}
		else
		{
			$this->flash('success', 'Pengaturan situs berhasil disimpan.');
		}

		redirect('admin/settings');
	}
}
