<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Variabel: $admin, $error
 */
$admin_name = isset($admin['name']) ? $admin['name'] : '';
$admin_user = isset($admin['username']) ? $admin['username'] : '';
?>

<div class="page-head">
	<div>
		<h1>Akun Saya</h1>
		<p>Perbarui nama, username, dan password administrator.</p>
	</div>
</div>

<?php if ( ! empty($error)): ?>
	<div class="alert alert-error"><?= html_escape($error) ?></div>
<?php endif; ?>

<?= form_open(site_url('admin/account')) ?>

	<div class="form-card">
		<div class="form-card-head">
			<h2>Profil</h2>
			<p>Nama ditampilkan pada topbar admin.</p>
		</div>

		<div class="form-card-body">
			<div class="form-row two">
				<div class="form-field half">
					<label class="field-label" for="acc-name">Nama Lengkap</label>
					<input type="text" class="input" id="acc-name" name="name" value="<?= html_escape(set_value('name', $admin_name)) ?>" required>
				</div>
				<div class="form-field half">
					<label class="field-label" for="acc-username">Username</label>
					<input type="text" class="input" id="acc-username" name="username" value="<?= html_escape(set_value('username', $admin_user)) ?>" autocapitalize="none" spellcheck="false" required>
				</div>
			</div>
		</div>
	</div>

	<div class="form-card" style="margin-top:16px">
		<div class="form-card-head">
			<h2>Ganti Password</h2>
			<p>Biarkan kosong bila tidak ingin mengubah password.</p>
		</div>

		<div class="form-card-body">
			<div class="form-row two">
				<div class="form-field full">
					<label class="field-label" for="acc-current">Password Saat Ini</label>
					<input type="password" class="input" id="acc-current" name="current_password" autocomplete="current-password">
					<span class="hint">Wajib diisi jika mengubah password.</span>
				</div>
				<div class="form-field half">
					<label class="field-label" for="acc-new">Password Baru</label>
					<input type="password" class="input" id="acc-new" name="new_password" autocomplete="new-password">
				</div>
				<div class="form-field half">
					<label class="field-label" for="acc-confirm">Konfirmasi Password Baru</label>
					<input type="password" class="input" id="acc-confirm" name="password_confirm" autocomplete="new-password">
				</div>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">
				<i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
			</button>
			<a class="btn btn-ghost" href="<?= site_url('admin') ?>">Batal</a>
		</div>
	</div>

<?= form_close() ?>
