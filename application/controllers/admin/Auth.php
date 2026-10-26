<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login & logout administrator.
 */
class Auth extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

	public function login()
	{
		if ( ! $this->is_installed())
		{
			redirect('setup');
		}

		if ($this->session->userdata('admin_id'))
		{
			redirect('admin');
		}

		$error = '';

		if ($this->input->method() === 'post')
		{
			$username = trim((string) $this->input->post('username', TRUE));
			$password = (string) $this->input->post('password', TRUE);

			if ($username === '' || $password === '')
			{
				$error = 'Username dan password wajib diisi.';
			}
			else
			{
				$user = $this->user_model->attempt($username, $password);

				if ($user !== NULL)
				{
					$this->session->sess_regenerate(TRUE);
					$this->session->set_userdata(array(
						'admin_id'   => (int) $user['id'],
						'admin_name' => $user['name'],
					));

					redirect('admin');
				}

				$error = 'Username atau password salah.';
			}
		}

		$this->load->view('admin/auth/login', array('error' => $error));
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('admin/login');
	}
}
