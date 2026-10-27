<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<title>Instalasi - <?= html_escape(isset($site['site_name']['en']) ? $site['site_name']['en'] : 'NusaTripper.com') ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
	<link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon-32x32.png') ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon-16x16.png') ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/apple-touch-icon.png') ?>">
</head>
<body class="auth-body">

<main class="auth-shell">
	<div class="auth-card">
		<div class="auth-brand">
			<img src="<?= base_url('assets/images/logo-icon.svg') ?>" alt="Logo" class="auth-logo" width="46" height="46">
			<div>
				<strong>NusaTripper.com</strong>
				<small>Instalasi Aplikasi</small>
			</div>
		</div>

		<?php if ($success): ?>
			<div class="alert alert-success">
				<strong>Instalasi berhasil.</strong>
				<p>Database, konten landing page, dan akun admin sudah dibuat. Silakan masuk ke dashboard.</p>
			</div>
			<a class="btn btn-primary btn-block" href="<?= site_url('admin/login') ?>">Masuk ke Admin</a>
		<?php else: ?>

			<h1 class="auth-title">Setup Awal</h1>
			<p class="auth-subtitle">Buat akun administrator untuk mulai mengelola konten landing page.</p>

			<?php if ($error !== ''): ?>
				<div class="alert alert-error"><?= html_escape($error) ?></div>
			<?php endif; ?>

			<form method="post" action="<?= site_url('setup') ?>" class="form-stack" autocomplete="off">
				<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

				<label class="field">
					<span class="field-label">Nama Lengkap</span>
					<input type="text" name="name" class="input" required maxlength="80" placeholder="Administrator">
				</label>

				<label class="field">
					<span class="field-label">Username</span>
					<input type="text" name="username" class="input" required maxlength="32" pattern="[a-zA-Z0-9._-]{3,32}" placeholder="admin" autocapitalize="none">
				</label>

				<label class="field">
					<span class="field-label">Password</span>
					<input type="password" name="password" class="input" required minlength="6" placeholder="Minimal 6 karakter">
				</label>

				<label class="field">
					<span class="field-label">Ulangi Password</span>
					<input type="password" name="password_confirm" class="input" required minlength="6" placeholder="Ulangi password">
				</label>

				<button type="submit" class="btn btn-primary btn-block btn-lg">Instal Sekarang</button>
			</form>

			<p class="auth-note">Setelah instalasi selesai, halaman ini otomatis tidak dapat diakses lagi.</p>
		<?php endif; ?>
	</div>
</main>

</body>
</html>
