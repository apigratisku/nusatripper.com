<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model pengaturan konten situs (key-value bilingual).
 */
class Setting_model extends CI_Model
{
	/** @var array|null cache pasangan setting */
	protected $pairs = NULL;

	/**
	 * Semua setting terurut.
	 *
	 * @return array
	 */
	public function all_rows()
	{
		return $this->db->order_by('sort_order', 'ASC')
			->order_by('id', 'ASC')
			->get('settings')
			->result_array();
	}

	/**
	 * Setting dikelompokkan per grup (untuk halaman admin).
	 *
	 * @return array
	 */
	public function grouped()
	{
		$groups = array();

		foreach ($this->all_rows() as $row)
		{
			$groups[$row['group_key']][] = $row;
		}

		return $groups;
	}

	/**
	 * Semua setting dalam bentuk key => ['en' => ..., 'id' => ...].
	 *
	 * @return array
	 */
	public function pairs()
	{
		if ($this->pairs !== NULL)
		{
			return $this->pairs;
		}

		$this->pairs = array();

		foreach ($this->all_rows() as $row)
		{
			$this->pairs[$row['setting_key']] = array(
				'en' => (string) $row['value_en'],
				'id' => (string) $row['value_id'],
			);
		}

		return $this->pairs;
	}

	/**
	 * Simpan banyak setting sekaligus.
	 *
	 * @param array $values key => ['en' => ..., 'id' => ...]
	 * @return void
	 */
	public function update_many(array $values)
	{
		foreach ($values as $key => $value)
		{
			$this->db->where('setting_key', $key)->update('settings', array(
				'value_en' => isset($value['en']) ? (string) $value['en'] : '',
				'value_id' => isset($value['id']) ? (string) $value['id'] : '',
			));
		}

		$this->pairs = NULL;
	}
}
