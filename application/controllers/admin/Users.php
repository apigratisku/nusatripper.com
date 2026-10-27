<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola akun administrator (multi-user).
 *
 * Hanya Super Admin yang boleh mengakses controller ini.
 * Pengaman penting:
 * - Tidak bisa menonaktifkan / menghapus akun sendiri.
 * - Tidak bisa mengubah role akun sendiri.
 * - Super Admin aktif terakhir tidak bisa dihapus / diturunkan / dinonaktifkan.
 */
class Users extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->require_capability('users.manage');
	}

	public function index()
	{
		$this->render('admin/users/index', array(
			'page_title' => 'Pengguna',
			'rows'       => $this->user_model->all(),
			'roles'      => User_model::roles(),
		));
	}

	public function create()
	{
		$error = '';
		$row   = $this->default_row();

		if ($this->input->method() === 'post')
		{
			$row   = $this->collect(0);
			$error = $this->validate($row, 0, TRUE);

			if ($error === '')
			{
				$this->user_model->create($row);
				$this->flash('success', 'Akun pengguna berhasil ditambahkan.');
				redirect('admin/users');
			}
		}

		$this->render_form($row, $error, 'Tambah Pengguna');
	}

	public function edit($id = 0)
	{
		$user = $this->user_model->find($id);

		if ($user === NULL)
		{
			show_404();
		}

		$error = '';

		if ($this->input->method() === 'post')
		{
			$data  = $this->collect($id);
			$error = $this->validate($data, $id, FALSE);

			if ($error === '')
			{
				$this->user_model->update_account($id, $data);

				if ($data['password'] !== '')
				{
					$this->user_model->update_password($id, $data['password']);
				}

				// Jaga sesi tetap sinkron bila mengubah akun sendiri.
				if ((int) $id === (int) $this->admin['id'])
				{
					$this->session->set_userdata('admin_name', $data['name']);
				}

				$this->flash('success', 'Akun pengguna berhasil diperbarui.');
				redirect('admin/users');
			}

			$user = array_merge($user, $data);
		}

		$this->render_form($user, $error, 'Edit Pengguna', $id);
	}

	public function toggle($id = 0)
	{
		if ($this->input->method() !== 'post')
		{
			show_404();
		}

		$user = $this->user_model->find($id);

		if ($user === NULL)
		{
			show_404();
		}

		if ((int) $id === (int) $this->admin['id'])
		{
			$this->flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
			redirect('admin/users');
		}

		$active = empty($user['is_active']);

		if ( ! $active && ! $this->has_other_superadmin($user))
		{
			$this->flash('error', 'Super Admin aktif terakhir tidak dapat dinonaktifkan.');
			redirect('admin/users');
		}

		$this->user_model->set_active($id, $active ? 1 : 0);
		$this->flash('success', 'Status akun berhasil diubah.');
		redirect('admin/users');
	}

	public function delete($id = 0)
	{
		if ($this->input->method() !== 'post')
		{
			show_404();
		}

		$user = $this->user_model->find($id);

		if ($user === NULL)
		{
			show_404();
		}

		if ((int) $id === (int) $this->admin['id'])
		{
			$this->flash('error', 'Anda tidak dapat menghapus akun sendiri.');
			redirect('admin/users');
		}

		if ( ! $this->has_other_superadmin($user))
		{
			$this->flash('error', 'Super Admin terakhir tidak dapat dihapus.');
			redirect('admin/users');
		}

		$this->user_model->delete($id);
		$this->flash('success', 'Akun pengguna berhasil dihapus.');
		redirect('admin/users');
	}

	// ------------------------------------------------------------------
	// Internal
	// ------------------------------------------------------------------

	/**
	 * Nilai default form tambah.
	 *
	 * @return array
	 */
	protected function default_row()
	{
		return array(
			'name'      => '',
			'username'  => '',
			'email'     => '',
			'role'      => User_model::ROLE_EDITOR,
			'is_active' => 1,
			'password'  => '',
		);
	}

	/**
	 * Ambil input form.
	 *
	 * @param int $id ID akun yang diedit (0 untuk akun baru)
	 * @return array
	 */
	protected function collect($id)
	{
		return array(
			'name'      => $this->post_text('name'),
			'username'  => $this->post_text('username'),
			'email'     => $this->post_text('email'),
			'role'      => $this->post_text('role'),
			'is_active' => $this->post_flag('is_active'),
			'password'  => (string) $this->input->post('password', TRUE),
			'confirm'   => (string) $this->input->post('password_confirm', TRUE),
		);
	}

	/**
	 * Validasi data akun.
	 *
	 * @param array $data
	 * @param int   $id
	 * @param bool  $is_create
	 * @return string pesan error (kosong bila valid)
	 */
	protected function validate(array $data, $id, $is_create)
	{
		$id = (int) $id;

		if ($data['name'] === '' || $data['username'] === '')
		{
			return 'Nama dan username wajib diisi.';
		}

		if ( ! preg_match('/^[a-zA-Z0-9._-]{3,32}$/', $data['username']))
		{
			return 'Username 3-32 karakter, hanya huruf, angka, titik, garis bawah, atau strip.';
		}

		$exists = $this->user_model->find_by_username($data['username']);

		if ($exists !== NULL && (int) $exists['id'] !== $id)
		{
			return 'Username sudah dipakai akun lain.';
		}

		if ($data['email'] !== '')
		{
			if ( ! filter_var($data['email'], FILTER_VALIDATE_EMAIL))
			{
				return 'Format email tidak valid.';
			}

			$same_mail = $this->user_model->find_by_email($data['email']);

			if ($same_mail !== NULL && (int) $same_mail['id'] !== $id)
			{
				return 'Email sudah dipakai akun lain.';
			}
		}

		$roles = User_model::roles();

		if ( ! isset($roles[$data['role']]))
		{
			return 'Role yang dipilih tidak valid.';
		}

		if ($id === (int) $this->admin['id'])
		{
			if ($data['role'] !== $this->role())
			{
				return 'Anda tidak dapat mengubah role akun sendiri.';
			}

			if ( ! $data['is_active'])
			{
				return 'Anda tidak dapat menonaktifkan akun sendiri.';
			}
		}

		if ($data['password'] !== '' || $is_create)
		{
			if (strlen($data['password']) < 6)
			{
				return 'Password minimal 6 karakter.';
			}

			if ($data['password'] !== $data['confirm'])
			{
				return 'Konfirmasi password tidak sama.';
			}
		}

		// Akun ini tetap harus menyisakan minimal satu Super Admin aktif.
		$target = $id > 0 ? $this->user_model->find($id) : NULL;

		if ($target !== NULL && ! empty($target['role']) && $target['role'] === User_model::ROLE_SUPERADMIN)
		{
			$loses_super = ($data['role'] !== User_model::ROLE_SUPERADMIN) || empty($data['is_active']);

			if ($loses_super && ! $this->has_other_superadmin($target))
			{
				return 'Harus ada minimal satu Super Admin aktif.';
			}
		}

		return '';
	}

	/**
	 * Apakah masih ada Super Admin aktif selain akun ini.
	 *
	 * @param array $user
	 * @return bool
	 */
	protected function has_other_superadmin(array $user)
	{
		$exclude = ! empty($user['id']) ? (int) $user['id'] : 0;

		return $this->user_model->count_other_superadmins($exclude) > 0;
	}

	/**
	 * @param array  $row
	 * @param string $error
	 * @param string $heading
	 * @param int    $id
	 * @return void
	 */
	protected function render_form(array $row, $error, $heading, $id = 0)
	{
		$this->render('admin/users/form', array(
			'page_title' => $heading,
			'row'        => $row,
			'error'      => $error,
			'heading'    => $heading,
			'roles'      => User_model::roles(),
			'user_id'    => (int) $id,
			'is_self'    => (int) $id === (int) $this->admin['id'],
		));
	}
}
