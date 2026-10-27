<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model akun administrator (multi-user + role).
 *
 * Role:
 *   superadmin : akses penuh (konten, pengaturan situs, kelola pengguna)
 *   editor     : mengelola konten (paket, galeri, keunggulan, testimoni, FAQ)
 *   viewer     : hanya melihat data admin
 */
class User_model extends CI_Model
{
	const ROLE_SUPERADMIN = 'superadmin';
	const ROLE_EDITOR     = 'editor';
	const ROLE_VIEWER     = 'viewer';

	/** @var bool kolom tambahan sudah dipastikan ada pada request ini */
	protected static $schema_ready = FALSE;

	public function __construct()
	{
		parent::__construct();

		$this->sync_schema();
	}

	// ------------------------------------------------------------------
	// Role
	// ------------------------------------------------------------------

	/**
	 * Daftar role yang tersedia beserta keterangannya.
	 *
	 * @return array
	 */
	public static function roles()
	{
		return array(
			self::ROLE_SUPERADMIN => array(
				'label' => 'Super Admin',
				'info'  => 'Akses penuh: konten, pengaturan situs, dan kelola pengguna.',
				'badge' => 'badge-hot',
			),
			self::ROLE_EDITOR => array(
				'label' => 'Editor',
				'info'  => 'Mengelola konten: paket, galeri, keunggulan, testimoni, dan FAQ.',
				'badge' => 'badge-teal',
			),
			self::ROLE_VIEWER => array(
				'label' => 'Viewer',
				'info'  => 'Hanya melihat data, tidak dapat mengubah apa pun.',
				'badge' => 'badge-navy',
			),
		);
	}

	/**
	 * Normalisasi nama role; nilai tak dikenal dianggap editor.
	 *
	 * @param string $role
	 * @return string
	 */
	public static function normalize_role($role)
	{
		$roles = self::roles();

		return isset($roles[$role]) ? $role : self::ROLE_EDITOR;
	}

	/**
	 * @param array $user
	 * @return bool
	 */
	public static function is_superadmin(array $user)
	{
		return isset($user['role']) && $user['role'] === self::ROLE_SUPERADMIN;
	}

	// ------------------------------------------------------------------
	// Skema (upgrade otomatis, idempoten)
	// ------------------------------------------------------------------

	/**
	 * Pastikan kolom multi-user tersedia pada tabel `users`.
	 * Aman dipanggil berkali-kali; hanya menambah kolom yang belum ada.
	 *
	 * @return void
	 */
	public function sync_schema()
	{
		if (self::$schema_ready || ! $this->db->table_exists('users'))
		{
			return;
		}

		self::$schema_ready = TRUE;

		$existing = $this->db->list_fields('users');

		// Definisi: kolom => [tipe SQLite, tipe MySQL]
		$columns = array(
			'email'         => array('TEXT', 'VARCHAR(191) NULL'),
			'role'          => array("TEXT NOT NULL DEFAULT 'editor'", "VARCHAR(20) NOT NULL DEFAULT 'editor'"),
			'is_active'     => array('INTEGER NOT NULL DEFAULT 1', 'TINYINT(1) NOT NULL DEFAULT 1'),
			'last_login_at' => array('TEXT', 'DATETIME NULL'),
			'last_login_ip' => array('TEXT', 'VARCHAR(45) NULL'),
		);

		$sqlite = strtolower($this->db->dbdriver) === 'sqlite3'
			|| (isset($this->db->subdriver) && strtolower($this->db->subdriver) === 'sqlite');

		foreach ($columns as $name => $types)
		{
			if (in_array($name, $existing, TRUE))
			{
				continue;
			}

			$type = $sqlite ? $types[0] : $types[1];

			$this->db->query('ALTER TABLE users ADD COLUMN '.$name.' '.$type);
		}

		// Akun terlama otomatis menjadi superadmin agar tidak terkunci.
		if ($this->count_by_role(self::ROLE_SUPERADMIN) < 1)
		{
			$first = $this->db->order_by('id', 'ASC')->limit(1)->get('users')->row_array();

			if ( ! empty($first))
			{
				$this->db->where('id', (int) $first['id'])->update('users', array(
					'role'      => self::ROLE_SUPERADMIN,
					'is_active' => 1,
				));
			}
		}
	}

	// ------------------------------------------------------------------
	// Query
	// ------------------------------------------------------------------

	/**
	 * Semua akun admin.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->db->order_by('id', 'ASC')->get('users')->result_array();
	}

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
	 * @param string $email
	 * @return array|null
	 */
	public function find_by_email($email)
	{
		if (trim((string) $email) === '')
		{
			return NULL;
		}

		$row = $this->db->where('email', trim($email))->get('users')->row_array();

		return empty($row) ? NULL : $row;
	}

	/**
	 * @param string $role
	 * @param bool   $only_active
	 * @return int
	 */
	public function count_by_role($role, $only_active = FALSE)
	{
		$this->db->where('role', $role);

		if ($only_active)
		{
			$this->db->where('is_active', 1);
		}

		return (int) $this->db->count_all_results('users');
	}

	/**
	 * Jumlah Super Admin aktif selain akun tertentu.
	 *
	 * @param int $exclude_id
	 * @return int
	 */
	public function count_other_superadmins($exclude_id = 0)
	{
		$this->db->where('role', self::ROLE_SUPERADMIN);
		$this->db->where('is_active', 1);

		if ((int) $exclude_id > 0)
		{
			$this->db->where('id !=', (int) $exclude_id);
		}

		return (int) $this->db->count_all_results('users');
	}

	// ------------------------------------------------------------------
	// Autentikasi
	// ------------------------------------------------------------------

	/**
	 * Verifikasi kombinasi username + password (akun harus aktif).
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

		if (isset($user['is_active']) && ! (int) $user['is_active'])
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
	 * Catat waktu & IP login terakhir.
	 *
	 * @param int $id
	 * @return void
	 */
	public function touch_login($id)
	{
		$this->db->where('id', (int) $id)->update('users', array(
			'last_login_at' => date('Y-m-d H:i:s'),
			'last_login_ip' => (string) $this->input->ip_address(),
		));
	}

	// ------------------------------------------------------------------
	// Mutasi
	// ------------------------------------------------------------------

	/**
	 * Buat akun admin baru.
	 *
	 * @param array $data name, username, email, password, role, is_active
	 * @return int ID akun baru
	 */
	public function create(array $data)
	{
		$this->db->insert('users', array(
			'name'       => $data['name'],
			'username'   => $data['username'],
			'email'      => isset($data['email']) ? $data['email'] : '',
			'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
			'role'       => self::normalize_role(isset($data['role']) ? $data['role'] : ''),
			'is_active'  => empty($data['is_active']) ? 0 : 1,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));

		return (int) $this->db->insert_id();
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
	 * Perbarui data akun dari halaman kelola pengguna.
	 *
	 * @param int   $id
	 * @param array $data name, username, email, role, is_active
	 * @return bool
	 */
	public function update_account($id, array $data)
	{
		return $this->db->where('id', (int) $id)->update('users', array(
			'name'       => $data['name'],
			'username'   => $data['username'],
			'email'      => isset($data['email']) ? $data['email'] : '',
			'role'       => self::normalize_role(isset($data['role']) ? $data['role'] : ''),
			'is_active'  => empty($data['is_active']) ? 0 : 1,
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

	/**
	 * Aktif / nonaktifkan akun.
	 *
	 * @param int $id
	 * @param int $active
	 * @return bool
	 */
	public function set_active($id, $active)
	{
		return $this->db->where('id', (int) $id)->update('users', array(
			'is_active'  => $active ? 1 : 0,
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('users');
	}
}
