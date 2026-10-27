<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Daftar pengguna admin.
 *
 * Variabel: $rows, $roles
 */
$rows  = isset($rows) ? $rows : array();
$roles = isset($roles) ? $roles : array();
?>

<div class="page-head">
	<div>
		<h1>Pengguna</h1>
		<p>Kelola akun administrator, role, dan status aktifnya.</p>
	</div>
	<div class="page-actions">
		<a class="btn btn-primary" href="<?= site_url('admin/users/create') ?>">
			<i class="fa-solid fa-user-plus"></i> Tambah Pengguna
		</a>
	</div>
</div>

<div class="card">
	<?php if (empty($rows)): ?>
		<div class="empty-state">
			<i class="fa-regular fa-user"></i>
			<p>Belum ada akun pengguna.</p>
			<a class="btn btn-primary btn-sm" href="<?= site_url('admin/users/create') ?>">Tambah Pengguna</a>
		</div>
	<?php else: ?>
		<div class="table-wrap">
			<table class="data-table">
				<thead>
					<tr>
						<th>Nama</th>
						<th>Email</th>
						<th>Role</th>
						<th>Status</th>
						<th>Login Terakhir</th>
						<th class="text-right">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($rows as $row): ?>
						<?php
						$role_key   = isset($row['role']) && isset($roles[$row['role']]) ? $row['role'] : 'editor';
						$role       = $roles[$role_key];
						$is_active  = ! empty($row['is_active']);
						$is_self    = (int) $row['id'] === (int) $admin['id'];
						?>
						<tr>
							<td data-label="Nama">
								<span class="cell-title"><?= html_escape($row['name']) ?></span>
								<span class="cell-sub">
									<?= html_escape($row['username']) ?><?= $is_self ? ' &middot; Anda' : '' ?>
								</span>
							</td>
							<td data-label="Email">
								<?= ! empty($row['email']) ? html_escape($row['email']) : '<span class="text-muted">&mdash;</span>' ?>
							</td>
							<td data-label="Role">
								<span class="badge <?= html_escape($role['badge']) ?>"><?= html_escape($role['label']) ?></span>
							</td>
							<td data-label="Status">
								<?php if ($is_active): ?>
									<span class="badge badge-active"><i class="fa-solid fa-circle-check"></i> Aktif</span>
								<?php else: ?>
									<span class="badge badge-inactive"><i class="fa-solid fa-ban"></i> Nonaktif</span>
								<?php endif; ?>
							</td>
							<td data-label="Login Terakhir">
								<?php if ( ! empty($row['last_login_at'])): ?>
									<span class="cell-title"><?= html_escape(date('d M Y H:i', strtotime($row['last_login_at']))) ?></span>
									<?php if ( ! empty($row['last_login_ip'])): ?>
										<span class="cell-sub"><?= html_escape($row['last_login_ip']) ?></span>
									<?php endif; ?>
								<?php else: ?>
									<span class="text-muted">Belum pernah</span>
								<?php endif; ?>
							</td>
							<td class="cell-actions">
								<a class="btn btn-outline btn-sm" href="<?= site_url('admin/users/edit/'.$row['id']) ?>">
									<i class="fa-solid fa-pen"></i> Edit
								</a>

								<?php if ( ! $is_self): ?>
									<?= form_open('admin/users/toggle/'.$row['id'], array('style' => 'display:inline-block')) ?>
										<button type="submit" class="btn btn-ghost btn-sm" title="Ubah status akun">
											<i class="fa-solid <?= $is_active ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
											<?= $is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
										</button>
									<?= form_close() ?>

									<?= form_open('admin/users/delete/'.$row['id'], array('style' => 'display:inline-block')) ?>
										<button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus akun <?= html_escape($row['username']) ?>? Tindakan tidak dapat dibatalkan.">
											<i class="fa-solid fa-trash"></i> Hapus
										</button>
									<?= form_close() ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>