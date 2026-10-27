<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller dasar untuk halaman publik.
 * Memuat seluruh setting situs (bilingual) ke dalam variabel view "site".
 */
class MY_Controller extends CI_Controller
{
	/** @var array setting situs: key => ['en' => ..., 'id' => ...] */
	protected $site = array();

	public function __construct()
	{
		parent::__construct();

		if ($this->db->table_exists('settings'))
		{
			$this->load->model('setting_model');
			$this->site = $this->setting_model->pairs();
		}

		$this->load->vars(array('site' => $this->site));
	}

	/**
	 * Apakah database sudah terpasang (tabel + data awal)?
	 *
	 * @return bool
	 */
	protected function is_installed()
	{
		return $this->db->table_exists('settings')
			&& $this->db->table_exists('users')
			&& $this->db->count_all('settings') > 0;
	}

	/**
	 * Ambil nilai setting dalam bahasa tertentu.
	 *
	 * @param string $key
	 * @param string $lang
	 * @return string
	 */
	protected function setting($key, $lang = 'en')
	{
		if ( ! isset($this->site[$key]))
		{
			return '';
		}

		$value = isset($this->site[$key][$lang]) ? $this->site[$key][$lang] : '';

		return ($lang !== 'en' && trim($value) === '') ? $this->site[$key]['en'] : $value;
	}
}

/**
 * Controller dasar area admin.
 * Menangani autentikasi sesi serta upload media (gambar / video).
 */
class Admin_Controller extends MY_Controller
{
	/** @var array data admin yang sedang login */
	protected $admin = array();

	/** @var string pesan error upload terakhir */
	protected $upload_error = '';

	/**
	 * Peta hak akses: capability => role yang diizinkan.
	 *
	 * @var array
	 */
	protected $capabilities = array(
		'users.manage'    => array('superadmin'),
		'settings.manage' => array('superadmin'),
		'content.view'    => array('superadmin', 'editor', 'viewer'),
		'content.write'   => array('superadmin', 'editor'),
	);

	public function __construct()
	{
		parent::__construct();

		if ( ! $this->is_installed())
		{
			redirect('setup');
		}

		$admin_id = (int) $this->session->userdata('admin_id');

		if ($admin_id < 1)
		{
			redirect('admin/login');
		}

		$this->load->model('user_model');
		$this->admin = $this->user_model->find($admin_id);

		// Akun dihapus atau dinonaktifkan: paksa logout.
		if (empty($this->admin) || (isset($this->admin['is_active']) && ! (int) $this->admin['is_active']))
		{
			$this->session->sess_destroy();
			redirect('admin/login');
		}

		$this->load->vars(array(
			'admin'     => $this->admin,
			'can_write' => $this->capability('content.write'),
		));
	}

	/**
	 * Role akun yang sedang login.
	 *
	 * @return string
	 */
	protected function role()
	{
		return isset($this->admin['role']) ? (string) $this->admin['role'] : 'viewer';
	}

	/**
	 * @return bool
	 */
	protected function is_superadmin()
	{
		return $this->role() === 'superadmin';
	}

	/**
	 * Apakah akun aktif memiliki capability tertentu.
	 *
	 * @param string $capability
	 * @return bool
	 */
	protected function capability($capability)
	{
		$allowed = isset($this->capabilities[$capability]) ? $this->capabilities[$capability] : array();

		return in_array($this->role(), $allowed, TRUE);
	}

	/**
	 * Hentikan request bila akun tidak berhak, lalu kembali ke dashboard.
	 *
	 * @param string $capability
	 * @return void
	 */
	protected function require_capability($capability)
	{
		if ($this->capability($capability))
		{
			return;
		}

		$this->flash('error', 'Anda tidak memiliki akses untuk halaman tersebut.');
		redirect('admin');
	}

	/**
	 * Proses upload file dari form.
	 *
	 * @param string $field         Nama input file
	 * @param string $allowed_types Ekstensi yang diizinkan (format CI)
	 * @param int    $max_kb
	 * @return string|null Path relatif "uploads/xxx.ext" atau NULL jika tidak ada file
	 */
	protected function do_upload($field, $allowed_types = 'jpg|jpeg|png|webp|gif', $max_kb = 25600)
	{
		$this->upload_error = '';

		if (empty($_FILES[$field]['name']))
		{
			return NULL;
		}

		$path = FCPATH.'uploads/';

		if ( ! is_dir($path) && ! @mkdir($path, 0755, TRUE))
		{
			$this->upload_error = 'Folder uploads tidak dapat dibuat. Periksa izin folder.';
			return NULL;
		}

		$config = array(
			'upload_path'      => $path,
			'allowed_types'    => $allowed_types,
			'max_size'         => $max_kb,
			'file_ext_tolower' => TRUE,
			'encrypt_name'     => TRUE,
			'remove_spaces'    => TRUE,
		);

		$this->load->library('upload');
		$this->upload->initialize($config, TRUE);

		if ( ! $this->upload->do_upload($field))
		{
			$this->upload_error = trim(strip_tags($this->upload->display_errors('', '')));
			return NULL;
		}

		$data = $this->upload->data();

		return 'uploads/'.$data['file_name'];
	}

	/**
	 * Hapus file hasil upload (hanya di dalam folder uploads/).
	 *
	 * @param string $path
	 * @return void
	 */
	protected function delete_media($path)
	{
		$path = ltrim((string) $path, '/');

		if (strpos($path, 'uploads/') === 0 && is_file(FCPATH.$path))
		{
			@unlink(FCPATH.$path);
		}
	}

	/**
	 * Nilai input teks dengan trim.
	 *
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	protected function post_text($name, $default = '')
	{
		$value = $this->input->post($name, TRUE);

		return ($value === NULL) ? $default : trim((string) $value);
	}

	/**
	 * Nilai checkbox 0 / 1.
	 *
	 * @param string $name
	 * @return int
	 */
	protected function post_flag($name)
	{
		return $this->input->post($name, TRUE) ? 1 : 0;
	}

	/**
	 * Render halaman admin memakai layout.
	 *
	 * @param string $view
	 * @param array  $data
	 * @return void
	 */
	protected function render($view, array $data = array())
	{
		$data['content'] = $this->load->view($view, $data, TRUE);

		$this->load->view('admin/layout', $data);
	}

	/**
	 * Pesan flash singkat.
	 *
	 * @param string $type success|error
	 * @param string $message
	 * @return void
	 */
	protected function flash($type, $message)
	{
		$this->session->set_flashdata($type, $message);
	}
}

/**
 * Controller CRUD generik untuk seluruh konten landing page.
 * Turunan cukup mendefinisikan model, judul, route, serta daftar field.
 */
class Admin_Resource extends Admin_Controller
{
	/** @var string nama model (mis. package_model) */
	protected $model_name = '';

	/** @var CI_Model */
	protected $model;

	/** @var string judul halaman */
	protected $title = 'Konten';

	/** @var string label satuan item */
	protected $singular = 'Item';

	/** @var string prefix URL admin, mis. admin/packages */
	protected $route = '';

	/** @var bool tampilkan field is_active */
	protected $has_status = TRUE;

	/** @var bool tampilkan field sort_order */
	protected $has_sort = TRUE;

	public function __construct()
	{
		parent::__construct();

		if ($this->model_name !== '')
		{
			$this->load->model($this->model_name);
			$this->model = $this->{$this->model_name};
		}

		$this->load->vars(array(
			'resource_title'    => $this->title,
			'resource_singular' => $this->singular,
			'resource_route'    => $this->route,
		));
	}

	/**
	 * Definisi field form.
	 *
	 * Setiap field: name, label, type, options, default, hint, width.
	 * Tipe: text, textarea, number, bilingual_text, bilingual_textarea,
	 *       image, video, select, checkbox, icon, rating.
	 *
	 * @return array
	 */
	protected function fields()
	{
		return array();
	}

	/**
	 * Definisi kolom tabel daftar.
	 *
	 * @return array
	 */
	protected function list_columns()
	{
		return array();
	}

	/**
	 * Semua field termasuk status & urutan.
	 *
	 * @return array
	 */
	protected function all_fields()
	{
		$fields = $this->fields();

		if ($this->has_status)
		{
			$fields[] = array('name' => 'is_active', 'label' => 'Tampilkan di Website', 'type' => 'checkbox', 'default' => 1, 'width' => 'half');
		}

		if ($this->has_sort)
		{
			$fields[] = array('name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0, 'width' => 'half');
		}

		return $fields;
	}

	/**
	 * Daftar field media (untuk pembersihan file).
	 *
	 * @return array
	 */
	protected function media_fields()
	{
		$names = array();

		foreach ($this->fields() as $field)
		{
			$type = isset($field['type']) ? $field['type'] : 'text';

			if ($type === 'image' || $type === 'video')
			{
				$names[] = $field['name'];
			}
		}

		return $names;
	}

	public function index()
	{
		$this->require_capability('content.view');

		$data = array(
			'rows'    => $this->model->all(),
			'columns' => $this->list_columns(),
		);

		$this->render('admin/resource/index', $data);
	}

	public function create()
	{
		$this->require_capability('content.write');

		$error = '';
		$row   = $this->default_row();

		if ($this->input->method() === 'post')
		{
			$this->upload_error = '';

			$data = $this->collect();

			if ($this->upload_error !== '')
			{
				$error = $this->upload_error;
				$row   = $data;
			}
			else
			{
				if ($this->has_sort && (int) $data['sort_order'] === 0)
				{
					$data['sort_order'] = $this->model->next_sort_order();
				}

				$this->model->create($data);
				$this->flash('success', $this->singular.' baru berhasil ditambahkan.');
				redirect($this->route);
			}
		}

		$this->render_form($row, $error, 'Tambah '.$this->singular);
	}

	public function edit($id = 0)
	{
		$this->require_capability('content.write');

		$row = $this->model->find($id);

		if ($row === NULL)
		{
			show_404();
		}

		$error = '';

		if ($this->input->method() === 'post')
		{
			$this->upload_error = '';

			$data = $this->collect();

			if ($this->upload_error !== '')
			{
				$error = $this->upload_error;
				$row   = array_merge($row, $data);
			}
			else
			{
				foreach ($this->media_fields() as $field)
				{
					if (isset($row[$field]) && $row[$field] !== $data[$field])
					{
						$this->delete_media($row[$field]);
					}
				}

				$this->model->update($id, $data);
				$this->flash('success', $this->singular.' berhasil diperbarui.');
				redirect($this->route);
			}
		}

		$this->render_form($row, $error, 'Edit '.$this->singular);
	}

	public function toggle($id = 0)
	{
		$this->require_capability('content.write');

		$row = $this->model->find($id);

		if ($row === NULL)
		{
			show_404();
		}

		$this->model->update($id, array('is_active' => empty($row['is_active']) ? 1 : 0));
		$this->flash('success', 'Status '.strtolower($this->singular).' diperbarui.');
		redirect($this->route);
	}

	public function delete($id = 0)
	{
		$this->require_capability('content.write');

		if ($this->input->method() !== 'post')
		{
			show_404();
		}

		$row = $this->model->delete($id);

		if ($row !== NULL)
		{
			foreach ($this->media_fields() as $field)
			{
				if ( ! empty($row[$field]))
				{
					$this->delete_media($row[$field]);
				}
			}

			$this->flash('success', $this->singular.' berhasil dihapus.');
		}

		redirect($this->route);
	}

	/**
	 * Nilai default form tambah.
	 *
	 * @return array
	 */
	protected function default_row()
	{
		$row = array();

		foreach ($this->all_fields() as $field)
		{
			$type = isset($field['type']) ? $field['type'] : 'text';
			$name = $field['name'];
			$default = isset($field['default']) ? $field['default'] : '';

			if ($type === 'bilingual_text' || $type === 'bilingual_textarea')
			{
				$row[$name.'_en'] = $default;
				$row[$name.'_id'] = $default;
			}
			else
			{
				$row[$name] = $default;
			}
		}

		return $row;
	}

	/**
	 * Ambil seluruh nilai input sesuai definisi field.
	 *
	 * @return array
	 */
	protected function collect()
	{
		$data = array();

		foreach ($this->all_fields() as $field)
		{
			$name = $field['name'];
			$type = isset($field['type']) ? $field['type'] : 'text';

			switch ($type)
			{
				case 'bilingual_text':
				case 'bilingual_textarea':
					$data[$name.'_en'] = $this->post_text($name.'_en');
					$data[$name.'_id'] = $this->post_text($name.'_id');
					break;

				case 'checkbox':
					$data[$name] = $this->post_flag($name);
					break;

				case 'number':
					$data[$name] = (int) $this->post_text($name, 0);
					break;

				case 'image':
				case 'video':
					$value = $this->post_text($name);

					if ($this->post_flag($name.'_remove'))
					{
						$value = '';
					}

					$upload = $this->do_upload(
						$name.'_file',
						$type === 'video' ? 'mp4|webm|ogg|mov' : 'jpg|jpeg|png|webp|gif'
					);

					if ($upload !== NULL)
					{
						$value = $upload;
					}

					$data[$name] = $value;
					break;

				case 'select':
					$value   = $this->post_text($name);
					$options = isset($field['options']) ? array_keys($field['options']) : array();
					$data[$name] = in_array($value, $options, TRUE) ? $value : (isset($field['default']) ? $field['default'] : '');
					break;

				default:
					$data[$name] = $this->post_text($name);
			}
		}

		return $data;
	}

	/**
	 * @param array  $row
	 * @param string $error
	 * @param string $heading
	 * @return void
	 */
	protected function render_form(array $row, $error, $heading)
	{
		$this->render('admin/resource/form', array(
			'row'     => $row,
			'fields'  => $this->all_fields(),
			'error'   => $error,
			'heading' => $heading,
		));
	}
}

