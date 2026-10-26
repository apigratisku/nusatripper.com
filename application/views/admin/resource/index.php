<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Daftar item generik untuk semua resource konten.
 *
 * Variabel: $rows, $columns, $resource_title, $resource_singular, $resource_route
 */
$columns = isset($columns) ? $columns : array();
$rows    = isset($rows) ? $rows : array();
?>

<div class="page-head">
	<div>
		<h1><?= html_escape($resource_title) ?></h1>
		<p>Kelola seluruh <?= html_escape(strtolower($resource_title)) ?> yang tampil di landing page.</p>
	</div>
	<div class="page-actions">
		<a class="btn btn-primary" href="<?= site_url($resource_route.'/create') ?>">
			<i class="fa-solid fa-plus"></i> Tambah <?= html_escape($resource_singular) ?>
		</a>
	</div>
</div>

<div class="card">
	<?php if (empty($rows)): ?>
		<div class="empty-state">
			<i class="fa-regular fa-folder-open"></i>
			<p>Belum ada <?= html_escape(strtolower($resource_title)) ?>. Tambahkan item pertama Anda.</p>
			<a class="btn btn-primary btn-sm" href="<?= site_url($resource_route.'/create') ?>">Tambah <?= html_escape($resource_singular) ?></a>
		</div>
	<?php else: ?>
		<div class="table-wrap">
			<table class="data-table">
				<thead>
					<tr>
						<?php foreach ($columns as $col): ?>
							<th><?= html_escape($col['label']) ?></th>
						<?php endforeach; ?>
						<th class="text-right">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($rows as $row): ?>
						<tr>
							<?php foreach ($columns as $col): ?>
								<?php
								$name  = $col['name'];
								$type  = isset($col['type']) ? $col['type'] : 'text';
								$label = $col['label'];
								?>
								<td data-label="<?= html_escape($label) ?>" class="cell-<?= html_escape($type) ?>">
									<?php if ($type === 'bilingual'): ?>
										<span class="cell-title"><?= html_escape(isset($row[$name.'_en']) ? $row[$name.'_en'] : '') ?></span>
										<?php if ( ! empty($row[$name.'_id'])): ?>
											<span class="cell-sub"><?= html_escape($row[$name.'_id']) ?></span>
										<?php endif; ?>

									<?php elseif ($type === 'image'): ?>
										<?php if ( ! empty($row[$name])): ?>
											<img class="thumb" src="<?= html_escape(media_url($row[$name])) ?>" alt="" loading="lazy">
										<?php else: ?>
											<span class="thumb thumb-placeholder"><i class="fa-regular fa-image"></i></span>
										<?php endif; ?>

									<?php elseif ($type === 'badge'): ?>
										<?php $style = isset($row['badge_style']) ? $row['badge_style'] : ''; ?>
										<?php if ( ! empty($row[$name.'_en'])): ?>
											<span class="badge <?= $style !== '' ? 'badge-'.html_escape($style) : 'badge-teal' ?>"><?= html_escape($row[$name.'_en']) ?></span>
										<?php else: ?>
											<span class="text-muted">&mdash;</span>
										<?php endif; ?>

									<?php elseif ($type === 'status'): ?>
										<?php if ( ! empty($row[$name])): ?>
											<span class="badge badge-active"><i class="fa-solid fa-eye"></i> Tampil</span>
										<?php else: ?>
											<span class="badge badge-inactive"><i class="fa-solid fa-eye-slash"></i> Disembunyikan</span>
										<?php endif; ?>

									<?php elseif ($type === 'order'): ?>
										<span class="badge badge-navy"><?= (int) $row[$name] ?></span>

									<?php else: ?>
										<?= html_escape(isset($row[$name]) ? $row[$name] : '') ?>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>

							<td class="cell-actions">
								<a class="btn btn-outline btn-sm" href="<?= site_url($resource_route.'/edit/'.$row['id']) ?>">
									<i class="fa-solid fa-pen"></i> Edit
								</a>

								<?php if (array_key_exists('is_active', $row)): ?>
									<?= form_open($resource_route.'/toggle/'.$row['id'], array('style' => 'display:inline-block')) ?>
										<button type="submit" class="btn btn-ghost btn-sm" title="Ubah status tampil">
											<i class="fa-solid <?= ! empty($row['is_active']) ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
											<?= ! empty($row['is_active']) ? 'Sembunyikan' : 'Tampilkan' ?>
										</button>
									<?= form_close() ?>
								<?php endif; ?>

								<?= form_open($resource_route.'/delete/'.$row['id'], array('style' => 'display:inline-block')) ?>
									<button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus <?= html_escape($resource_singular) ?> ini? Tindakan tidak dapat dibatalkan.">
										<i class="fa-solid fa-trash"></i> Hapus
									</button>
								<?= form_close() ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>
