<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Installer awal: membuat skema database + mengisi konten landing page.
 * Hanya dapat dijalankan selama tabel "settings" belum ada.
 */
class Setup extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function index()
	{
		if ($this->db->table_exists('settings') && $this->db->count_all('settings') > 0)
		{
			redirect('admin/login');
		}

		$data = array(
			'error'    => '',
			'success'  => FALSE,
		);

		if ($this->input->method() === 'post')
		{
			$name     = trim((string) $this->input->post('name', TRUE));
			$username = trim((string) $this->input->post('username', TRUE));
			$password = (string) $this->input->post('password', TRUE);
			$confirm  = (string) $this->input->post('password_confirm', TRUE);

			if ($name === '' || $username === '')
			{
				$data['error'] = 'Nama dan username wajib diisi.';
			}
			elseif ( ! preg_match('/^[a-zA-Z0-9._-]{3,32}$/', $username))
			{
				$data['error'] = 'Username 3-32 karakter, hanya huruf, angka, titik, garis bawah, atau strip.';
			}
			elseif (strlen($password) < 6)
			{
				$data['error'] = 'Password minimal 6 karakter.';
			}
			elseif ($password !== $confirm)
			{
				$data['error'] = 'Konfirmasi password tidak sama.';
			}
			else
			{
				try
				{
					$this->install($name, $username, $password);
					$data['success'] = TRUE;
				}
				catch (Exception $e)
				{
					$data['error'] = 'Instalasi gagal: '.$e->getMessage();
				}
			}
		}

		$this->load->view('setup/index', $data);
	}

	/**
	 * Membuat seluruh tabel dan mengisi data awal.
	 *
	 * @param string $name
	 * @param string $username
	 * @param string $password
	 * @return void
	 */
	private function install($name, $username, $password)
	{
		$this->load->dbforge();

		$this->create_settings();
		$this->create_users();
		$this->create_packages();
		$this->create_gallery();
		$this->create_why_points();
		$this->create_testimonials();
		$this->create_faqs();

		$seed = include APPPATH.'seed/landing_seed.php';

		if ( ! is_array($seed))
		{
			throw new Exception('File seed tidak valid.');
		}

		$this->seed_settings($seed['settings']);
		$this->seed_table('packages', $seed['packages']);
		$this->seed_table('gallery', $seed['gallery']);
		$this->seed_table('why_points', $seed['why_points']);
		$this->seed_table('testimonials', $seed['testimonials']);
		$this->seed_table('faqs', $seed['faqs']);

		$this->db->insert('users', array(
			'username'   => $username,
			'password'   => password_hash($password, PASSWORD_DEFAULT),
			'name'       => $name,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	private function create_settings()
	{
		$this->dbforge->add_field(array(
			'id'          => array('type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => TRUE),
			'setting_key' => array('type' => 'TEXT'),
			'group_key'   => array('type' => 'TEXT'),
			'label'       => array('type' => 'TEXT'),
			'field_type'  => array('type' => 'TEXT'),
			'sort_order'  => array('type' => 'INTEGER', 'default' => 0),
			'value_en'    => array('type' => 'TEXT'),
			'value_id'    => array('type' => 'TEXT'),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('settings', TRUE);
		$this->db->query('CREATE UNIQUE INDEX IF NOT EXISTS idx_settings_key ON settings (setting_key)');
	}

	private function create_users()
	{
		$this->dbforge->add_field(array(
			'id'         => array('type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => TRUE),
			'username'   => array('type' => 'TEXT'),
			'password'   => array('type' => 'TEXT'),
			'name'       => array('type' => 'TEXT'),
			'created_at' => array('type' => 'TEXT'),
			'updated_at' => array('type' => 'TEXT'),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users', TRUE);
		$this->db->query('CREATE UNIQUE INDEX IF NOT EXISTS idx_users_username ON users (username)');
	}

	private function create_packages()
	{
		$this->dbforge->add_field(array(
			'id'              => array('type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => TRUE),
			'category'        => array('type' => 'TEXT'),
			'badge_en'        => array('type' => 'TEXT'),
			'badge_id'        => array('type' => 'TEXT'),
			'badge_style'     => array('type' => 'TEXT'),
			'title_en'        => array('type' => 'TEXT'),
			'title_id'        => array('type' => 'TEXT'),
			'description_en'  => array('type' => 'TEXT'),
			'description_id'  => array('type' => 'TEXT'),
			'inclusions_en'   => array('type' => 'TEXT'),
			'inclusions_id'   => array('type' => 'TEXT'),
			'duration_en'     => array('type' => 'TEXT'),
			'duration_id'     => array('type' => 'TEXT'),
			'price_label_en'  => array('type' => 'TEXT'),
			'price_label_id'  => array('type' => 'TEXT'),
			'price'           => array('type' => 'TEXT'),
			'price_unit'      => array('type' => 'TEXT'),
			'rating'          => array('type' => 'TEXT'),
			'image'           => array('type' => 'TEXT'),
			'wa_message'      => array('type' => 'TEXT'),
			'is_active'       => array('type' => 'INTEGER', 'default' => 1),
			'sort_order'      => array('type' => 'INTEGER', 'default' => 0),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('packages', TRUE);
	}

	private function create_gallery()
	{
		$this->dbforge->add_field(array(
			'id'             => array('type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => TRUE),
			'media_type'     => array('type' => 'TEXT'),
			'size'           => array('type' => 'TEXT'),
			'image'          => array('type' => 'TEXT'),
			'video'          => array('type' => 'TEXT'),
			'category_en'    => array('type' => 'TEXT'),
			'category_id'    => array('type' => 'TEXT'),
			'title_en'       => array('type' => 'TEXT'),
			'title_id'       => array('type' => 'TEXT'),
			'description_en' => array('type' => 'TEXT'),
			'description_id' => array('type' => 'TEXT'),
			'is_active'      => array('type' => 'INTEGER', 'default' => 1),
			'sort_order'     => array('type' => 'INTEGER', 'default' => 0),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('gallery', TRUE);
	}

	private function create_why_points()
	{
		$this->dbforge->add_field(array(
			'id'             => array('type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => TRUE),
			'icon'           => array('type' => 'TEXT'),
			'title_en'       => array('type' => 'TEXT'),
			'title_id'       => array('type' => 'TEXT'),
			'description_en' => array('type' => 'TEXT'),
			'description_id' => array('type' => 'TEXT'),
			'is_active'      => array('type' => 'INTEGER', 'default' => 1),
			'sort_order'     => array('type' => 'INTEGER', 'default' => 0),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('why_points', TRUE);
	}

	private function create_testimonials()
	{
		$this->dbforge->add_field(array(
			'id'        => array('type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => TRUE),
			'name'      => array('type' => 'TEXT'),
			'initials'  => array('type' => 'TEXT'),
			'rating'    => array('type' => 'INTEGER', 'default' => 5),
			'origin_en' => array('type' => 'TEXT'),
			'origin_id' => array('type' => 'TEXT'),
			'quote_en'  => array('type' => 'TEXT'),
			'quote_id'  => array('type' => 'TEXT'),
			'is_active' => array('type' => 'INTEGER', 'default' => 1),
			'sort_order'=> array('type' => 'INTEGER', 'default' => 0),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('testimonials', TRUE);
	}

	private function create_faqs()
	{
		$this->dbforge->add_field(array(
			'id'          => array('type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => TRUE),
			'question_en' => array('type' => 'TEXT'),
			'question_id' => array('type' => 'TEXT'),
			'answer_en'   => array('type' => 'TEXT'),
			'answer_id'   => array('type' => 'TEXT'),
			'is_active'   => array('type' => 'INTEGER', 'default' => 1),
			'sort_order'  => array('type' => 'INTEGER', 'default' => 0),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('faqs', TRUE);
	}

	/**
	 * @param array $settings
	 * @return void
	 */
	private function seed_settings(array $settings)
	{
		$order = 0;

		foreach ($settings as $row)
		{
			$order++;

			$this->db->insert('settings', array(
				'setting_key' => $row['key'],
				'group_key'   => $row['group'],
				'label'       => $row['label'],
				'field_type'  => isset($row['type']) ? $row['type'] : 'text',
				'sort_order'  => $order,
				'value_en'    => isset($row['en']) ? (string) $row['en'] : '',
				'value_id'    => isset($row['id']) ? (string) $row['id'] : '',
			));
		}
	}

	/**
	 * @param string $table
	 * @param array  $rows
	 * @return void
	 */
	private function seed_table($table, array $rows)
	{
		$order = 0;

		foreach ($rows as $row)
		{
			$order++;

			if ( ! isset($row['sort_order']))
			{
				$row['sort_order'] = $order;
			}

			$this->db->insert($table, $row);
		}
	}
}
