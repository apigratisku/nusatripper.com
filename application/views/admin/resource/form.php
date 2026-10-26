<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Form tambah / edit generik.
 *
 * Variabel: $row, $fields, $error, $heading,
 *           $resource_title, $resource_singular, $resource_route
 */
$is_edit = isset($row['id']) && (int) $row['id'] > 0;
$action  = $is_edit
	? site_url($resource_route.'/edit/'.$row['id'])
	: site_url($resource_route.'/create');

$value = function ($name) use ($row) {
	return isset($row[$name]) ? $row[$name] : '';
};

$is_media = function ($type) {
	return $type === 'image' || $type === 'video';
};
?>

<div class="page-head">
	<div>
		<h1><?= html_escape($heading) ?></h1>
		<p><a href="<?= site_url($resource_route) ?>">&larr; Kembali ke <?= html_escape($resource_title) ?></a></p>
	</div>
</div>

<?php if ($error !== ''): ?>
	<div class="alert alert-error"><?= html_escape($error) ?></div>
<?php endif; ?>

<?= form_open($action, array('class' => 'form-card', 'enctype' => 'multipart/form-data')) ?>

	<div class="form-card-head">
		<h2><?= html_escape($resource_singular) ?></h2>
		<p>Kolom bertanda bahasa (EN / ID) akan tampil bergantian sesuai pilihan bahasa pengunjung.</p>
	</div>

	<div class="form-card-body">
		<div class="form-row two">
			<?php foreach ($fields as $field): ?>
				<?php
				$name  = $field['name'];
				$type  = isset($field['type']) ? $field['type'] : 'text';
				$label = isset($field['label']) ? $field['label'] : $name;
				$hint  = isset($field['hint']) ? $field['hint'] : '';
				$width = isset($field['width']) ? $field['width'] : 'full';
				$val   = $value($name);

				// Field bilingual & media selalu memakai lebar penuh.
				if ($type === 'bilingual_text' || $type === 'bilingual_textarea' || $is_media($type))
				{
					$width = 'full';
				}
				?>
				<div class="form-field <?= $width === 'half' ? 'half' : 'full' ?>">
					<label class="field-label" for="f-<?= html_escape($name) ?>"><?= html_escape($label) ?></label>

					<?php if ($type === 'bilingual_text' || $type === 'bilingual_textarea'): ?>
						<?php
						$en_val = $value($name.'_en');
						$id_val = $value($name.'_id');
						$tag    = $type === 'bilingual_textarea' ? 'textarea' : 'input';
						?>
						<div class="bilingual-box">
							<div>
								<span class="lang-tag"><span class="dot"></span> English</span>
								<?php if ($tag === 'textarea'): ?>
									<textarea class="textarea" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>_en" rows="3"><?= html_escape($en_val) ?></textarea>
								<?php else: ?>
									<input type="text" class="input" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>_en" value="<?= html_escape($en_val) ?>">
								<?php endif; ?>
							</div>
							<div>
								<span class="lang-tag"><span class="dot id"></span> Indonesia</span>
								<?php if ($tag === 'textarea'): ?>
									<textarea class="textarea" name="<?= html_escape($name) ?>_id" rows="3"><?= html_escape($id_val) ?></textarea>
								<?php else: ?>
									<input type="text" class="input" name="<?= html_escape($name) ?>_id" value="<?= html_escape($id_val) ?>">
								<?php endif; ?>
							</div>
						</div>

					<?php elseif ($type === 'textarea'): ?>
						<textarea class="textarea" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>" rows="4"><?= html_escape($val) ?></textarea>

					<?php elseif ($type === 'number'): ?>
						<input type="number" class="input" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>" value="<?= html_escape($val) ?>" step="1">

					<?php elseif ($type === 'rating'): ?>
						<select class="select" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>">
							<?php for ($i = 1; $i <= 5; $i++): ?>
								<option value="<?= $i ?>"<?= (int) $val === $i ? ' selected' : '' ?>><?= $i ?> bintang</option>
							<?php endfor; ?>
						</select>

					<?php elseif ($type === 'select'): ?>
						<?php $options = isset($field['options']) ? $field['options'] : array(); ?>
						<select class="select" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>">
							<?php foreach ($options as $opt_value => $opt_label): ?>
								<option value="<?= html_escape($opt_value) ?>"<?= (string) $val === (string) $opt_value ? ' selected' : '' ?>><?= html_escape($opt_label) ?></option>
							<?php endforeach; ?>
						</select>

					<?php elseif ($type === 'checkbox'): ?>
						<label class="checkbox">
							<input type="checkbox" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>" value="1"<?= ! empty($val) ? ' checked' : '' ?>>
							<span>Aktif / tampilkan</span>
						</label>

					<?php elseif ($type === 'icon'): ?>
						<div class="flex items-center gap-8">
							<input type="text" class="input" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>" value="<?= html_escape($val) ?>" placeholder="fa-solid fa-star">
							<span class="badge badge-navy" style="font-size:16px"><i class="<?= html_escape($val) ?>"></i></span>
						</div>

					<?php elseif ($is_media($type)): ?>
						<input type="hidden" name="<?= html_escape($name) ?>" value="<?= html_escape($val) ?>">

						<div class="media-box">
							<?php if ($val !== '' && $type === 'image'): ?>
								<img class="media-preview" id="preview-<?= html_escape($name) ?>" src="<?= html_escape(media_url($val)) ?>" alt="Pratinjau">
							<?php elseif ($val !== '' && $type === 'video'): ?>
								<video class="media-preview is-video" id="preview-<?= html_escape($name) ?>" src="<?= html_escape(media_url($val)) ?>" controls muted playsinline preload="metadata"></video>
							<?php else: ?>
								<div class="media-empty" id="empty-<?= html_escape($name) ?>">
									<i class="fa-regular <?= $type === 'video' ? 'fa-circle-play' : 'fa-image' ?>" style="font-size:20px"></i>
									<span>Belum ada <?= $type === 'video' ? 'video' : 'gambar' ?></span>
								</div>
								<?php if ($type === 'video'): ?>
									<video class="media-preview is-video" id="preview-<?= html_escape($name) ?>" hidden controls muted playsinline></video>
								<?php else: ?>
									<img class="media-preview" id="preview-<?= html_escape($name) ?>" alt="Pratinjau" hidden>
								<?php endif; ?>
							<?php endif; ?>

							<input
								type="file"
								class="input"
								name="<?= html_escape($name) ?>_file"
								accept="<?= $type === 'video' ? 'video/*' : 'image/*' ?>"
								data-preview="#preview-<?= html_escape($name) ?>"
								data-empty="#empty-<?= html_escape($name) ?>"
							>

							<?php if ($val !== ''): ?>
								<label class="checkbox">
									<input type="checkbox" name="<?= html_escape($name) ?>_remove" value="1">
									<span>Hapus <?= $type === 'video' ? 'video' : 'gambar' ?> saat ini</span>
								</label>
							<?php endif; ?>

							<input type="text" class="input" value="<?= html_escape($val) ?>" placeholder="atau tempel URL / path file" disabled>
						</div>
					<?php else: ?>
						<input type="text" class="input" id="f-<?= html_escape($name) ?>" name="<?= html_escape($name) ?>" value="<?= html_escape($val) ?>">
					<?php endif; ?>

					<?php if ($hint !== ''): ?>
						<span class="hint"><?= html_escape($hint) ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> <?= $is_edit ? 'Simpan Perubahan' : 'Simpan' ?>
		</button>
		<a class="btn btn-ghost" href="<?= site_url($resource_route) ?>">Batal</a>
	</div>

<?= form_close() ?>
