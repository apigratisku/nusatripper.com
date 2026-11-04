<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'models/Content_model.php';

/**
 * Item galeri (foto / video).
 */
class Gallery_model extends Content_model
{
	protected $table = 'gallery';

	/**
	 * Item galeri milik satu paket (untuk galeri di kartu paket).
	 *
	 * @param int $package_id
	 * @return array
	 */
	public function by_package($package_id)
	{
		return $this->db
			->where('is_active', 1)
			->where('package_id', (int) $package_id)
			->order_by('sort_order', 'ASC')
			->order_by('id', 'ASC')
			->get($this->table)
			->result_array();
	}

	/**
	 * Galeri umum (tidak terikat paket tertentu) untuk section #gallery.
	 *
	 * @return array
	 */
	public function general()
	{
		return $this->db
			->where('is_active', 1)
			->where('package_id', 0)
			->order_by('sort_order', 'ASC')
			->order_by('id', 'ASC')
			->get($this->table)
			->result_array();
	}
}
