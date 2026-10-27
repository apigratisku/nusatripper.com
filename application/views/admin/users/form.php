<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Form tambah / edit akun pengguna admin.
 *
 * Variabel: $row, $error, $heading, $roles, $user_id, $is_self
 */
$is_edit = isset($user_id) && (int) $user_id > 0;
$action  = $is_edit ? site_url('admin/users/edit/'.$user_id) : site_url('admin/users/create');

$val = function ($key, $default = '') use ($row) {
	if (isset($row[$key]))
	{
		return $row[$key];
	}

	return $default;
};

$current_role = $val('role', User_model::ROLE_EDITOR);
$is_active    = (int) $val('is_active', 1);
?>

<div class="page-head">
	<div>
		<h1><?= html_escape($heading) ?></h1>
		<p><a href="<?= site_url('admin/users') ?>">&larr; Kembali ke daftar pengguna</a></p>
	</div>
</div>

<?php if ($error !== ''): ?>
	<div class="alert alert-error"><?= html_escape($error) ?></div>
<?php endif; ?>

<?= form_open($action, array('class' => 'form-card')) ?>

	<div class="form-card-head">
		<h2>Data Pengguna</h2>
		<p>Informasi dasar untuk masuk ke panel administrator.</p>
	</div>

	<div class="form-card-body">
		<div class="form-row two">
			<div class="form-field half">
				<label class="field-label" for="u-name">Nama Lengkap</label>
				<input type="text" class="input" id="u-name" name="name" value="<?= html_escape($val('name')) ?>" required autofocus>
			</div>

			<div class="form-field half">
				<label class="field-label" for="u-username">Username</label>
				<input type="text" class="input" id="u-username" name="username" value="<?= html_escape($val('username')) ?>" autocapitalize="none" spellcheck="false" required>
				<span class="hint">3-32 karakter huruf, angka, titik, strip, atau garis bawah.</span>
			</div>

			<div class="form-field half">
				<label class="field-label" for="u-email">Email (opsional)</label>
				<input type="email" class="input" id="u-email" name="email" value="<?= html_escape($val('email')) ?>" placeholder="nama@nusatripper.com">
			</div>

			<div class="form-field half">
				<label class="field-label" for="u-role">Role</label>
				<?php if ($is_self): ?>
					<input type="hidden" name="role" value="<?= html_escape($current_role) ?>">
					<input type="text" class="input" value="<?= html_escape(isset($roles[$current_role]) ? $roles[$current_role]['label'] : $current_role) ?>" disabled>
					<span class="hint">Anda tidak dapat mengubah role akun sendiri.</span>
				<?php else: ?>
					<select class="select" id="u-role" name="role">
						<?php foreach ($roles as $r_key => $r_data): ?>
							<option value="<?= html_escape($r_key) ?>"<?= $current_role === $r_key ? ' selected' : '' ?>>
								<?= html_escape($r_data['label']) ?> &mdash; <?= html_escape($r_data['info']) ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</div>

			<div class="form-field full">
				<label class="checkbox">
					<?php if ($is_self): ?>
						<input type="hidden" name="is_active" value="1">
						<input type="checkbox" checked disabled>
						<span>Akun Aktif (tidak dapat menonaktifkan akun sendiri)</span>
					<?php else: ?>
						<input type="checkbox" id="u-active" name="is_active" value="1"<?= $is_active ? ' checked' : '' ?>>
						<span>Akun Aktif (dapat login ke panel administrator)</span>
					<?php endif; ?>
				</label>
			</div>
		</div>
	</div>

	<div class="form-card-head" style="border-top:1px solid var(--border,#e2e8f0);margin-top:8px">
		<h2><?= $is_edit ? 'Ganti Password' : 'Password Awal' ?></h2>
		<p><?= $is_edit ? 'Kosongkan bila tidak ingin mengubah password akun ini.' : 'Wajib diisi minimal 6 karakter.' ?></p>
	</div>

	<div class="form-card-body">
		<div class="form-row two">
			<div class="form-field half">
				<label class="field-label" for="u-pass">Password<?= $is_edit ? ' Baru' : '' ?></label>
				<input type="password" class="input" id="u-pass" name="password" autocomplete="new-password"<?= $is_edit ? '' : ' required' ?>>
				<span class="hint">Minimal 6 karakter.</span>
			</div>

			<div class="form-field half">
				<label class="field-label" for="u-pass-confirm">Konfirmasi Password</label>
				<input type="password" class="input" id="u-pass-confirm" name="password_confirm" autocomplete="new-password"<?= $is_edit ? '' : ' required' ?>>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Pengguna' ?>
		</button>
		<a class="btn btn-ghost" href="<?= site_url('admin/users') ?>">Batal</a>
	</div>

<?= form_close() ?>
