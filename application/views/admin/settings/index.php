<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Form pengaturan situs (bilingual).
 *
 * Variabel: $grouped, $group_labels, $group_icons
 */
$total = 0;

foreach ($grouped as $rows)
{
	$total += count($rows);
}
?>

<div class="page-head">
	<div>
		<h1>Pengaturan Situs</h1>
		<p>Kelola teks bilingual, SEO, kontak, media global, dan label navigasi (<?= (int) $total ?> field).</p>
	</div>
	<div class="page-actions">
		<button type="submit" form="settingsForm" class="btn btn-primary btn-sm">
			<i class="fa-solid fa-floppy-disk"></i> Simpan Semua
		</button>
	</div>
</div>

<?= form_open(site_url('admin/settings'), array(
	'id'      => 'settingsForm',
	'enctype' => 'multipart/form-data',
)) ?>

	<?php foreach ($grouped as $group_key => $rows): ?>
		<?php
		$group_label = isset($group_labels[$group_key]) ? $group_labels[$group_key] : ucfirst($group_key);
		$group_icon  = isset($group_icons[$group_key]) ? $group_icons[$group_key] : 'fa-solid fa-gear';
		?>
		<section class="card settings-group">
			<div class="card-head">
				<h2><i class="<?= html_escape($group_icon) ?>" style="color:var(--teal)"></i> <?= html_escape($group_label) ?></h2>
				<p><?= count($rows) ?> field</p>
			</div>
			<div class="card-body">
				<div class="settings-list">
					<?php foreach ($rows as $row): ?>
						<?php
						$key   = $row['setting_key'];
						$type  = $row['field_type'];
						$val_en = (string) $row['value_en'];
						$val_id = (string) $row['value_id'];
						$is_media = ($type === 'image' || $type === 'video');
						?>
						<div class="settings-row">
							<div>
								<div class="label"><?= html_escape($row['label']) ?></div>
								<div class="key"><?= html_escape($key) ?> &middot; <?= html_escape($type) ?></div>
							</div>

							<?php if ($is_media): ?>
								<div class="media-box">
									<?php if ($val_en !== '' && $type === 'image'): ?>
										<img class="media-preview" id="preview-<?= html_escape($key) ?>" src="<?= html_escape(media_url($val_en)) ?>" alt="Pratinjau <?= html_escape($row['label']) ?>">
									<?php elseif ($val_en !== '' && $type === 'video'): ?>
										<video class="media-preview is-video" id="preview-<?= html_escape($key) ?>" src="<?= html_escape(media_url($val_en)) ?>" controls muted playsinline preload="metadata"></video>
									<?php else: ?>
										<div class="media-empty" id="empty-<?= html_escape($key) ?>">
											<i class="fa-regular <?= $type === 'video' ? 'fa-circle-play' : 'fa-image' ?>" style="font-size:20px"></i>
											<span>Belum ada <?= $type === 'video' ? 'video' : 'gambar' ?></span>
										</div>
										<?php if ($type === 'video'): ?>
											<video class="media-preview is-video" id="preview-<?= html_escape($key) ?>" hidden controls muted playsinline></video>
										<?php else: ?>
											<img class="media-preview" id="preview-<?= html_escape($key) ?>" alt="Pratinjau" hidden>
										<?php endif; ?>
									<?php endif; ?>

									<input
										type="file"
										class="input"
										name="<?= html_escape($key) ?>_file"
										accept="<?= $type === 'video' ? 'video/*' : 'image/*' ?>"
										data-preview="#preview-<?= html_escape($key) ?>"
										data-empty="#empty-<?= html_escape($key) ?>"
									>

									<?php if ($val_en !== ''): ?>
										<label class="checkbox">
											<input type="checkbox" name="<?= html_escape($key) ?>_remove" value="1">
											<span>Hapus <?= $type === 'video' ? 'video' : 'gambar' ?> saat ini</span>
										</label>
										<span class="hint">File saat ini: <?= html_escape($val_en) ?></span>
									<?php endif; ?>
								</div>

							<?php else: ?>
								<div class="settings-inputs">
									<label class="field">
										<span class="lang-tag"><span class="dot"></span> English</span>
										<?php if ($type === 'textarea'): ?>
											<textarea class="textarea" name="value_en[<?= html_escape($key) ?>]" rows="3"><?= html_escape($val_en) ?></textarea>
										<?php elseif ($type === 'icon'): ?>
											<div class="flex items-center gap-8">
												<input type="text" class="input" name="value_en[<?= html_escape($key) ?>]" value="<?= html_escape($val_en) ?>" placeholder="fa-solid fa-star">
												<span class="badge badge-navy" style="font-size:16px"><i class="<?= html_escape($val_en) ?>"></i></span>
											</div>
										<?php else: ?>
											<input type="text" class="input" name="value_en[<?= html_escape($key) ?>]" value="<?= html_escape($val_en) ?>">
										<?php endif; ?>
									</label>

									<label class="field">
										<span class="lang-tag"><span class="dot id"></span> Indonesia</span>
										<?php if ($type === 'textarea'): ?>
											<textarea class="textarea" name="value_id[<?= html_escape($key) ?>]" rows="3"><?= html_escape($val_id) ?></textarea>
										<?php elseif ($type === 'icon'): ?>
											<div class="flex items-center gap-8">
												<input type="text" class="input" name="value_id[<?= html_escape($key) ?>]" value="<?= html_escape($val_id) ?>" placeholder="fa-solid fa-star">
												<span class="badge badge-navy" style="font-size:16px"><i class="<?= html_escape($val_id !== '' ? $val_id : $val_en) ?>"></i></span>
											</div>
										<?php else: ?>
											<input type="text" class="input" name="value_id[<?= html_escape($key) ?>]" value="<?= html_escape($val_id) ?>">
										<?php endif; ?>
									</label>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endforeach; ?>

	<div class="form-actions" style="border:1px solid var(--border);border-radius:var(--radius);margin-bottom:18px">
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan
		</button>
		<a class="btn btn-ghost" href="<?= site_url('admin') ?>">Batal</a>
	</div>

<?= form_close() ?>
