<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model akun admin.
 */
class User_model extends CI_Model
{
	/**
	 * @param int $id
	 * @return array|null
	 */
	public function find($id)
	{
		$row = $this->db->where('id', (int) $id)->get('users')->row_array();

		return empty($row) ? NULL : $row;
	}

	/**
	 * @param string $username
	 * @return array|null
	 */
	public function find_by_username($username)
	{
		$row = $this->db->where('username', $username)->get('users')->row_array();

		return empty($row) ? NULL : $row;
	}

	/**
	 * Verifikasi kombinasi username + password.
	 *
	 * @param string $username
	 * @param string $password
	 * @return array|null
	 */
	public function attempt($username, $password)
	{
		$user = $this->find_by_username(trim($username));

		if ($user === NULL || ! password_verify($password, $user['password']))
		{
			return NULL;
		}

		if (password_needs_rehash($user['password'], PASSWORD_DEFAULT))
		{
			$this->update_password($user['id'], $password);
		}

		return $user;
	}

	/**
	 * Perbarui profil (nama + username).
	 *
	 * @param int    $id
	 * @param string $username
	 * @param string $name
	 * @return bool
	 */
	public function update_profile($id, $username, $name)
	{
		return $this->db->where('id', (int) $id)->update('users', array(
			'username'   => $username,
			'name'       => $name,
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	/**
	 * @param int    $id
	 * @param string $password plain text
	 * @return bool
	 */
	public function update_password($id, $password)
	{
		return $this->db->where('id', (int) $id)->update('users', array(
			'password'   => password_hash($password, PASSWORD_DEFAULT),
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}
}
