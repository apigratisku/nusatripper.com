<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$brand = isset($site['brand_title']['en']) && $site['brand_title']['en'] !== ''
	? $site['brand_title']['en']
	: 'NusaTripper.com';
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="robots" content="noindex, nofollow">
	<title>Masuk &middot; Admin <?= html_escape($brand) ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
	<meta name="theme-color" content="#0A2540">
</head>
<body class="auth-body">

<div class="auth-shell">
	<div class="auth-card">
		<div class="auth-brand">
			<span class="auth-logo"><i class="fa-solid fa-compass"></i></span>
			<div>
				<strong><?= html_escape($brand) ?></strong>
				<small>Panel Administrator</small>
			</div>
		</div>

		<h1 class="auth-title">Masuk ke Panel</h1>
		<p class="auth-subtitle">Kelola paket wisata, galeri, dan konten landing page.</p>

		<?php if ( ! empty($error)): ?>
			<div class="alert alert-error"><?= html_escape($error) ?></div>
		<?php endif; ?>

		<?= form_open(site_url('admin/login'), array('class' => 'form-stack', 'autocomplete' => 'on')) ?>

			<label class="field">
				<span class="field-label">Username</span>
				<input
					type="text"
					class="input"
					name="username"
					value="<?= html_escape(set_value('username')) ?>"
					placeholder="admin"
					autocomplete="username"
					autocapitalize="none"
					spellcheck="false"
					required
					autofocus
				>
			</label>

			<label class="field">
				<span class="field-label">Password</span>
				<input
					type="password"
					class="input"
					name="password"
					placeholder="••••••••"
					autocomplete="current-password"
					required
				>
			</label>

			<button type="submit" class="btn btn-primary btn-lg btn-block">
				<i class="fa-solid fa-right-to-bracket"></i> Masuk
			</button>

		<?= form_close() ?>

		<p class="auth-note">
			<a href="<?= site_url('/') ?>">&larr; Kembali ke website</a>
		</p>
	</div>
</div>

</body>
</html>
