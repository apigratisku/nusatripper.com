<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Variabel: $stats (packages, packages_on, gallery, gallery_on,
 *           why, testimonials, faqs, settings)
 */
$cards = array(
	array(
		'label' => 'Paket Wisata',
		'value' => $stats['packages'],
		'sub'   => $stats['packages_on'].' tampil',
		'icon'  => 'fa-solid fa-suitcase-rolling',
		'class' => 'teal',
		'url'   => 'admin/packages',
	),
	array(
		'label' => 'Galeri',
		'value' => $stats['gallery'],
		'sub'   => $stats['gallery_on'].' tampil',
		'icon'  => 'fa-solid fa-images',
		'class' => 'gold',
		'url'   => 'admin/gallery',
	),
	array(
		'label' => 'Keunggulan',
		'value' => $stats['why'],
		'sub'   => 'Poin keunggulan',
		'icon'  => 'fa-solid fa-medal',
		'class' => 'coral',
		'url'   => 'admin/why',
	),
	array(
		'label' => 'Testimoni',
		'value' => $stats['testimonials'],
		'sub'   => 'Ulasan pelanggan',
		'icon'  => 'fa-solid fa-quote-left',
		'class' => '',
		'url'   => 'admin/testimonials',
	),
	array(
		'label' => 'FAQ',
		'value' => $stats['faqs'],
		'sub'   => 'Pertanyaan umum',
		'icon'  => 'fa-solid fa-circle-question',
		'class' => 'teal',
		'url'   => 'admin/faqs',
	),
	array(
		'label' => 'Pengaturan',
		'value' => $stats['settings'],
		'sub'   => 'Field konten situs',
		'icon'  => 'fa-solid fa-sliders',
		'class' => 'gold',
		'url'   => 'admin/settings',
	),
);
?>

<div class="page-head">
	<div>
		<h1>Dashboard</h1>
		<p>Ringkasan konten dan pintasan pengelolaan website.</p>
	</div>
	<div class="page-actions">
		<a class="btn btn-outline btn-sm" href="<?= site_url('admin/settings') ?>">
			<i class="fa-solid fa-sliders"></i> Pengaturan
		</a>
		<a class="btn btn-primary btn-sm" href="<?= site_url('admin/packages/create') ?>">
			<i class="fa-solid fa-plus"></i> Paket Baru
		</a>
	</div>
</div>

<div class="stat-grid">
	<?php foreach ($cards as $card): ?>
		<a class="stat-card" href="<?= site_url($card['url']) ?>" style="text-decoration:none;color:inherit">
			<span class="stat-icon <?= html_escape($card['class']) ?>"><i class="<?= html_escape($card['icon']) ?>"></i></span>
			<span>
				<span class="stat-value"><?= (int) $card['value'] ?></span>
				<span class="stat-label" style="display:block"><?= html_escape($card['label']) ?> &middot; <?= html_escape($card['sub']) ?></span>
			</span>
		</a>
	<?php endforeach; ?>
</div>

<div class="card">
	<div class="card-head">
		<h2>Kelola Konten</h2>
		<p>Semua konten landing page dapat diubah dari sini.</p>
	</div>
	<div class="card-body">
		<div class="stack">
			<a class="btn btn-outline" href="<?= site_url('admin/settings') ?>">
				<i class="fa-solid fa-pen-to-square"></i> Teks, SEO, Kontak &amp; Media Global
			</a>
			<a class="btn btn-outline" href="<?= site_url('admin/packages') ?>">
				<i class="fa-solid fa-suitcase-rolling"></i> Paket Wisata
			</a>
			<a class="btn btn-outline" href="<?= site_url('admin/gallery') ?>">
				<i class="fa-solid fa-images"></i> Galeri Foto &amp; Video
			</a>
			<a class="btn btn-outline" href="<?= site_url('admin/why') ?>">
				<i class="fa-solid fa-medal"></i> Keunggulan
			</a>
			<a class="btn btn-outline" href="<?= site_url('admin/testimonials') ?>">
				<i class="fa-solid fa-quote-left"></i> Testimoni
			</a>
			<a class="btn btn-outline" href="<?= site_url('admin/faqs') ?>">
				<i class="fa-solid fa-circle-question"></i> FAQ
			</a>
			<a class="btn btn-outline" href="<?= site_url('admin/account') ?>">
				<i class="fa-solid fa-user-gear"></i> Akun Saya
			</a>
		</div>
	</div>
</div>
