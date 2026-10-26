<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model dasar untuk semua konten yang dapat di-CRUD dari admin.
 */
class Content_model extends CI_Model
{
	/** @var string nama tabel */
	protected $table = '';

	/** @var string kolom pengurutan */
	protected $order_column = 'sort_order';

	/**
	 * Semua baris (termasuk yang nonaktif).
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->db->order_by($this->order_column, 'ASC')
			->order_by('id', 'ASC')
			->get($this->table)
			->result_array();
	}

	/**
	 * Baris aktif untuk halaman publik.
	 *
	 * @return array
	 */
	public function active()
	{
		return $this->db->where('is_active', 1)
			->order_by($this->order_column, 'ASC')
			->order_by('id', 'ASC')
			->get($this->table)
			->result_array();
	}

	/**
	 * @param int $id
	 * @return array|null
	 */
	public function find($id)
	{
		$row = $this->db->where('id', (int) $id)->get($this->table)->row_array();

		return empty($row) ? NULL : $row;
	}

	/**
	 * @param array $data
	 * @return int ID baru
	 */
	public function create(array $data)
	{
		$this->db->insert($this->table, $data);

		return (int) $this->db->insert_id();
	}

	/**
	 * @param int   $id
	 * @param array $data
	 * @return bool
	 */
	public function update($id, array $data)
	{
		return $this->db->where('id', (int) $id)->update($this->table, $data);
	}

	/**
	 * @param int $id
	 * @return array|null baris yang dihapus (untuk membersihkan file)
	 */
	public function delete($id)
	{
		$row = $this->find($id);

		if ($row !== NULL)
		{
			$this->db->where('id', (int) $id)->delete($this->table);
		}

		return $row;
	}

	/**
	 * @return int
	 */
	public function count_all()
	{
		return (int) $this->db->count_all($this->table);
	}

	/**
	 * @return int
	 */
	public function count_active()
	{
		return (int) $this->db->where('is_active', 1)->count_all_results($this->table);
	}

	/**
	 * Urutan berikutnya saat menambah data baru.
	 *
	 * @return int
	 */
	public function next_sort_order()
	{
		$row = $this->db->select_max($this->order_column, 'max_order')->get($this->table)->row_array();

		return empty($row['max_order']) ? 1 : ((int) $row['max_order'] + 1);
	}
}
