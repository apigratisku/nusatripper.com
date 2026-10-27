<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$current = $this->uri->segment(2);
$current = ($current === NULL || $current === '') ? 'dashboard' : $current;

$nav = array(
	array('key' => 'dashboard', 'label' => 'Dashboard', 'url' => 'admin', 'icon' => 'fa-solid fa-gauge-high'),
	array('key' => 'settings', 'label' => 'Pengaturan Situs', 'url' => 'admin/settings', 'icon' => 'fa-solid fa-sliders'),
	array('key' => 'packages', 'label' => 'Paket Wisata', 'url' => 'admin/packages', 'icon' => 'fa-solid fa-suitcase-rolling'),
	array('key' => 'gallery', 'label' => 'Galeri', 'url' => 'admin/gallery', 'icon' => 'fa-solid fa-images'),
	array('key' => 'why', 'label' => 'Keunggulan', 'url' => 'admin/why', 'icon' => 'fa-solid fa-medal'),
	array('key' => 'testimonials', 'label' => 'Testimoni', 'url' => 'admin/testimonials', 'icon' => 'fa-solid fa-quote-left'),
	array('key' => 'faqs', 'label' => 'FAQ', 'url' => 'admin/faqs', 'icon' => 'fa-solid fa-circle-question'),
);

$role = isset($admin['role']) ? $admin['role'] : 'viewer';

$account_nav = array();
if ($role === 'superadmin') {
	$account_nav[] = array('key' => 'users', 'label' => 'Kelola Pengguna', 'url' => 'admin/users', 'icon' => 'fa-solid fa-users-gear');
}
$account_nav[] = array('key' => 'account', 'label' => 'Akun Saya', 'url' => 'admin/account', 'icon' => 'fa-solid fa-user-gear');

$page_title = isset($resource_title) ? $resource_title : (isset($page_title) ? $page_title : 'Dashboard');
$admin_name = isset($admin['name']) && $admin['name'] !== '' ? $admin['name'] : 'Administrator';
$brand = isset($site['brand_title']['en']) && $site['brand_title']['en'] !== '' ? $site['brand_title']['en'] : 'NusaTripper.com';
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="robots" content="noindex, nofollow">
	<title><?= html_escape($page_title) ?> &middot; Admin <?= html_escape($brand) ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
	<meta name="theme-color" content="#0A2540">
	<link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon-32x32.png') ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon-16x16.png') ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/apple-touch-icon.png') ?>">
</head>
<body class="admin-body">

<div class="admin-backdrop" id="adminBackdrop" hidden></div>

<aside class="admin-sidebar" id="adminSidebar" aria-label="Menu admin">
	<div class="sidebar-brand">
		<img src="<?= base_url('assets/images/logo-icon.svg') ?>" alt="Logo" class="auth-logo" width="40" height="40">
		<div>
			<strong><?= html_escape($brand) ?></strong>
			<small>Panel Administrator</small>
		</div>
	</div>

	<nav class="sidebar-nav">
		<span class="sidebar-group">Konten</span>

		<?php foreach ($nav as $item): ?>
			<?php if ($item['key'] === 'settings' && $role !== 'superadmin') continue; ?>
			<a class="sidebar-link<?= $current === $item['key'] ? ' is-active' : '' ?>" href="<?= site_url($item['url']) ?>">
				<i class="<?= html_escape($item['icon']) ?>"></i>
				<span><?= html_escape($item['label']) ?></span>
			</a>
		<?php endforeach; ?>

		<span class="sidebar-group">Sistem</span>

		<?php foreach ($account_nav as $item): ?>
			<a class="sidebar-link<?= $current === $item['key'] ? ' is-active' : '' ?>" href="<?= site_url($item['url']) ?>">
				<i class="<?= html_escape($item['icon']) ?>"></i>
				<span><?= html_escape($item['label']) ?></span>
			</a>
		<?php endforeach; ?>

		<a class="sidebar-link" href="<?= site_url('admin/logout') ?>">
			<i class="fa-solid fa-right-from-bracket"></i>
			<span>Keluar</span>
		</a>
	</nav>

	<div class="sidebar-footer">
		<?= date('Y') ?> &copy; <?= html_escape($brand) ?>
	</div>
</aside>

<div class="admin-main">
	<header class="admin-topbar">
		<button type="button" class="icon-btn menu-toggle" id="menuToggle" aria-label="Buka menu" aria-controls="adminSidebar" aria-expanded="false">
			<i class="fa-solid fa-bars"></i>
		</button>

		<span class="topbar-title"><?= html_escape($page_title) ?></span>

		<div class="topbar-user">
			<i class="fa-solid fa-circle-user" style="font-size:18px;color:#94a3b8"></i>
			<strong><?= html_escape($admin_name) ?></strong>
		</div>

		<a class="icon-btn" href="<?= site_url('/') ?>" target="_blank" rel="noopener" title="Lihat website">
			<i class="fa-solid fa-arrow-up-right-from-square"></i>
		</a>
	</header>

	<main class="admin-content">
		<?php if ($this->session->flashdata('success')): ?>
			<div class="alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div>
		<?php endif; ?>

		<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-error"><?= html_escape($this->session->flashdata('error')) ?></div>
		<?php endif; ?>

		<?= $content ?>
	</main>
</div>

<script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>
