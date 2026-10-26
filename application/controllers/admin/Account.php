<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pengaturan akun administrator yang sedang login.
 */
class Account extends Admin_Controller
{
	public function index()
	{
		$error = '';

		if ($this->input->method() === 'post')
		{
			$name     = $this->post_text('name');
			$username = $this->post_text('username');
			$current  = (string) $this->input->post('current_password', TRUE);
			$password = (string) $this->input->post('new_password', TRUE);
			$confirm  = (string) $this->input->post('password_confirm', TRUE);

			if ($name === '' || $username === '')
			{
				$error = 'Nama dan username wajib diisi.';
			}
			elseif ( ! preg_match('/^[a-zA-Z0-9._-]{3,32}$/', $username))
			{
				$error = 'Username 3-32 karakter, hanya huruf, angka, titik, garis bawah, atau strip.';
			}
			else
			{
				$exists = $this->user_model->find_by_username($username);

				if ($exists !== NULL && (int) $exists['id'] !== (int) $this->admin['id'])
				{
					$error = 'Username sudah dipakai akun lain.';
				}
			}

			if ($error === '' && $password !== '')
			{
				if ( ! password_verify($current, $this->admin['password']))
				{
					$error = 'Password saat ini salah.';
				}
				elseif (strlen($password) < 6)
				{
					$error = 'Password baru minimal 6 karakter.';
				}
				elseif ($password !== $confirm)
				{
					$error = 'Konfirmasi password baru tidak sama.';
				}
			}

			if ($error === '')
			{
				$this->user_model->update_profile($this->admin['id'], $username, $name);

				if ($password !== '')
				{
					$this->user_model->update_password($this->admin['id'], $password);
				}

				$this->session->set_userdata('admin_name', $name);
				$this->flash('success', 'Data akun berhasil diperbarui.');
				redirect('admin/account');
			}
		}

		$this->render('admin/account', array(
			'page_title' => 'Akun Saya',
			'error'      => $error,
		));
	}
}
