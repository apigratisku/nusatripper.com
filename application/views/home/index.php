<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Landing page dinamis.
 *
 * Semua teks bilingual dirender sebagai data-en / data-id sehingga tombol
 * bahasa dapat mengganti konten tanpa reload.
 *
 * @var array $site         setting situs: key => ['en' => ..., 'id' => ...]
 * @var array $packages
 * @var array $gallery
 * @var array $why_points
 * @var array $testimonials
 * @var array $faqs
 */

// --- Pembantu lokal -----------------------------------------------------
$S = function ($key) use ($site) {
	return (isset($site[$key]) && is_array($site[$key]))
		? $site[$key]
		: array('en' => '', 'id' => '');
};

$val = function ($key, $lang = 'en') use ($S) {
	$pair  = $S($key);
	$value = isset($pair[$lang]) ? $pair[$lang] : '';

	return ($lang !== 'en' && trim($value) === '') ? $pair['en'] : $value;
};

$media = function ($key) use ($S) {
	$pair = $S($key);

	return media_url($pair['en']);
};

$photo = function ($path) {
	return media_url($path);
};

$wa_number = preg_replace('/[^0-9]/', '', $val('wa_number'));
$wa_base   = wa_link($wa_number);

$phone     = $val('phone_display');
$tel_href  = 'tel:'.preg_replace('/[^0-9+]/', '', $phone);

$categories = array(
	'diving'    => array('label' => 'filter_diving', 'icon' => 'fa-solid fa-mask-snorkel'),
	'island'    => array('label' => 'filter_island', 'icon' => 'fa-solid fa-mountain-sun'),
	'transport' => array('label' => 'filter_transport', 'icon' => 'fa-solid fa-car'),
);

$badge_class = array('hot' => 'badge-hot', 'popular' => 'badge-popular');
$perk_icons  = array('fa-camera', 'fa-utensils', 'fa-life-ring', 'fa-van-shuttle');
$trust_icons = array('fa-certificate', 'fa-shield-halved', 'fa-bolt');

// Link footer "Label|URL" per baris, dipasangkan EN/ID berdasarkan urutan.
$parse_links = function ($en_key, $id_key) use ($val) {
	$rows = lines_pair($val($en_key), $val($id_key));
	$out  = array();

	foreach ($rows as $row)
	{
		$en = explode('|', $row['en'], 2);
		$id = explode('|', $row['id'], 2);

		$out[] = array(
			'url'   => isset($en[1]) ? trim($en[1]) : '#',
			'label' => array('en' => trim($en[0]), 'id' => trim($id[0])),
		);
	}

	return $out;
};

$footer_packages     = $parse_links('footer_links_en', 'footer_links_id');
$footer_destinations = $parse_links('footer_dest_en', 'footer_dest_id');

$meta_title  = $val('meta_title');
$meta_desc   = $val('meta_description');
$og_image    = $media('og_image');
$canonical   = $val('canonical_url');
$hero_bg     = $media('hero_image');
$brand_title = $val('brand_title');
$brand_sub   = $val('brand_subtitle');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

	<title><?= html_escape($meta_title) ?></title>
	<meta name="title" content="<?= html_escape($meta_title) ?>">
	<meta name="description" content="<?= html_escape($meta_desc) ?>">
	<meta name="keywords" content="<?= html_escape($val('meta_keywords')) ?>">
	<meta name="author" content="<?= html_escape($brand_title) ?>">
	<meta name="robots" content="index, follow">
	<meta name="theme-color" content="#0A2540">
	<?php if ($canonical !== ''): ?>
	<link rel="canonical" href="<?= html_escape($canonical) ?>">
	<?php endif; ?>

	<!-- Open Graph -->
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?= html_escape($meta_title) ?>">
	<meta property="og:description" content="<?= html_escape($meta_desc) ?>">
	<?php if ($canonical !== ''): ?>
	<meta property="og:url" content="<?= html_escape($canonical) ?>">
	<?php endif; ?>
	<?php if ($og_image !== ''): ?>
	<meta property="og:image" content="<?= html_escape($og_image) ?>">
	<?php endif; ?>

	<!-- Fonts & Icons -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

	<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "TravelAgency",
		"name": <?= json_encode($val('site_name') !== '' ? $val('site_name') : $brand_title) ?>,
		"description": <?= json_encode($meta_desc) ?>,
		"url": <?= json_encode($canonical !== '' ? $canonical : base_url()) ?>,
		"telephone": <?= json_encode('+'.$wa_number) ?>,
		"address": {
			"@type": "PostalAddress",
			"addressLocality": "Lombok",
			"addressRegion": "Nusa Tenggara Barat",
			"addressCountry": "ID"
		},
		"areaServed": <?= json_encode($val('location')) ?>,
		"aggregateRating": {
			"@type": "AggregateRating",
			"ratingValue": <?= json_encode($val('testi_rating')) ?>,
			"reviewCount": <?= json_encode($val('testi_review_count')) ?>
		}
	}
	</script>
</head>
<body>

	<!-- Top Announcement Bar -->
	<div class="top-bar">
		<div class="container top-bar-container">
			<div class="top-info">
				<span class="info-loc"><i class="fa-solid fa-location-dot"></i> <span class="loc-text" <?= i18n_attr($S('location_short')) ?>><?= i18n_text($S('location_short')) ?></span></span>
				<?php if ($phone !== ''): ?>
				<span class="info-separator">•</span>
				<a href="<?= html_escape($tel_href) ?>" class="info-phone"><i class="fa-solid fa-phone"></i> <?= html_escape($phone) ?></a>
				<?php endif; ?>
			</div>
			<div class="lang-switch-wrapper">
				<span class="lang-label"><i class="fa-solid fa-globe"></i></span>
				<button type="button" id="langToggleBtn" class="lang-btn" aria-label="Switch Language">
					<span class="active-lang">EN</span> | <span>ID</span>
				</button>
			</div>
		</div>
	</div>

	<!-- Header / Navigation -->
	<header class="site-header" id="navbar">
		<div class="container nav-container">
			<a href="#home" class="brand-logo" aria-label="<?= html_escape($brand_title) ?>">
				<div class="logo-icon"><i class="fa-solid fa-compass"></i></div>
				<div class="logo-text">
					<span class="brand-title" <?= i18n_attr($S('brand_title')) ?>><?= i18n_text($S('brand_title')) ?></span>
					<span class="brand-subtitle" <?= i18n_attr($S('brand_subtitle')) ?>><?= i18n_text($S('brand_subtitle')) ?></span>
				</div>
			</a>

			<nav class="nav-menu" id="navMenu" aria-label="Main navigation">
				<a href="#home" class="nav-link active" <?= i18n_attr($S('nav_home')) ?>><?= i18n_text($S('nav_home')) ?></a>
				<a href="#packages" class="nav-link" <?= i18n_attr($S('nav_packages')) ?>><?= i18n_text($S('nav_packages')) ?></a>
				<a href="#whaleshark" class="nav-link" <?= i18n_attr($S('nav_whaleshark')) ?>><?= i18n_text($S('nav_whaleshark')) ?></a>
				<a href="#whyus" class="nav-link" <?= i18n_attr($S('nav_whyus')) ?>><?= i18n_text($S('nav_whyus')) ?></a>
				<a href="#gallery" class="nav-link" <?= i18n_attr($S('nav_gallery')) ?>><?= i18n_text($S('nav_gallery')) ?></a>
				<a href="#faq" class="nav-link" <?= i18n_attr($S('nav_faq')) ?>><?= i18n_text($S('nav_faq')) ?></a>
				<a href="<?= html_escape(wa_link($wa_number, $val('wa_default_message'))) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary nav-cta">
					<i class="fa-brands fa-whatsapp"></i> <span <?= i18n_attr($S('nav_cta')) ?>><?= i18n_text($S('nav_cta')) ?></span>
				</a>
			</nav>

			<button type="button" class="hamburger-btn" id="mobileMenuBtn" aria-label="Toggle Navigation" aria-controls="navMenu" aria-expanded="false">
				<i class="fa-solid fa-bars"></i>
			</button>
		</div>
	</header>

	<!-- Hero -->
	<section class="hero-section" id="home"<?= $hero_bg !== '' ? ' style="background-image:url('.html_escape($hero_bg).')"' : '' ?>>
		<div class="hero-overlay"></div>
		<div class="container hero-container">
			<div class="hero-content">
				<div class="badge-pill">
					<i class="fa-solid fa-water"></i> <span <?= i18n_attr($S('hero_badge')) ?>><?= i18n_text($S('hero_badge')) ?></span>
				</div>
				<h1 class="hero-title" <?= i18n_attr($S('hero_title')) ?>><?= i18n_text($S('hero_title')) ?></h1>
				<p class="hero-subtitle" <?= i18n_attr($S('hero_subtitle')) ?>><?= i18n_text($S('hero_subtitle')) ?></p>
				<div class="hero-cta-group">
					<a href="#packages" class="btn btn-primary btn-lg">
						<span <?= i18n_attr($S('hero_btn_explore')) ?>><?= i18n_text($S('hero_btn_explore')) ?></span> <i class="fa-solid fa-arrow-down"></i>
					</a>
					<a href="<?= html_escape(wa_link($wa_number, $val('hero_wa_message'))) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-lg">
						<i class="fa-brands fa-whatsapp"></i> <span <?= i18n_attr($S('hero_btn_chat')) ?>><?= i18n_text($S('hero_btn_chat')) ?></span>
					</a>
				</div>

				<div class="trust-badges">
					<?php for ($i = 1; $i <= 3; $i++): ?>
					<div class="trust-badge-item">
						<i class="<?= html_escape($val('trust_'.$i.'_icon') !== '' ? $val('trust_'.$i.'_icon') : $trust_icons[$i - 1]) ?>"></i>
						<div>
							<strong <?= i18n_attr($S('trust_'.$i.'_title')) ?>><?= i18n_text($S('trust_'.$i.'_title')) ?></strong>
							<small <?= i18n_attr($S('trust_'.$i.'_sub')) ?>><?= i18n_text($S('trust_'.$i.'_sub')) ?></small>
						</div>
					</div>
					<?php endfor; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Featured Expedition -->
	<section class="featured-expedition" id="whaleshark">
		<div class="container">
			<div class="featured-card">
				<div class="featured-img-col">
					<div class="featured-media-wrapper">
						<?php $featured_video = $media('featured_video'); $featured_poster = $media('featured_poster'); ?>
						<?php if ($featured_video !== ''): ?>
						<video class="featured-video" autoplay muted loop playsinline preload="metadata"<?= $featured_poster !== '' ? ' poster="'.html_escape($featured_poster).'"' : '' ?>>
							<source src="<?= html_escape($featured_video) ?>" type="video/mp4">
						</video>
						<?php elseif ($featured_poster !== ''): ?>
						<img class="featured-video" src="<?= html_escape($featured_poster) ?>" alt="<?= html_escape($val('featured_title')) ?>" loading="lazy" decoding="async">
						<?php endif; ?>
						<div class="img-badge">
							<i class="fa-solid fa-star"></i> <span <?= i18n_attr($S('featured_badge')) ?>><?= i18n_text($S('featured_badge')) ?></span>
						</div>
					</div>
				</div>
				<div class="featured-text-col">
					<span class="section-tag" <?= i18n_attr($S('featured_tag')) ?>><?= i18n_text($S('featured_tag')) ?></span>
					<h2 class="featured-title" <?= i18n_attr($S('featured_title')) ?>><?= i18n_text($S('featured_title')) ?></h2>
					<p class="featured-desc" <?= i18n_attr($S('featured_desc')) ?>><?= i18n_text($S('featured_desc')) ?></p>

					<div class="featured-rates">
						<?php for ($i = 1; $i <= 2; $i++): ?>
							<?php if ($val('featured_rate_'.$i.'_price') !== ''): ?>
							<div class="rate-badge">
								<span class="rate-type"><i class="<?= $i === 1 ? 'fa-solid fa-water' : 'fa-solid fa-person-swimming' ?>"></i> <span <?= i18n_attr($S('featured_rate_'.$i.'_label')) ?>><?= i18n_text($S('featured_rate_'.$i.'_label')) ?></span>:</span>
								<strong class="rate-price" <?= i18n_attr($S('featured_rate_'.$i.'_price')) ?>><?= i18n_text($S('featured_rate_'.$i.'_price')) ?></strong>
								<?php if ($val('featured_rate_'.$i.'_note') !== ''): ?>
								<small class="rate-note" <?= i18n_attr($S('featured_rate_'.$i.'_note')) ?>><?= i18n_text($S('featured_rate_'.$i.'_note')) ?></small>
								<?php endif; ?>
							</div>
							<?php endif; ?>
						<?php endfor; ?>
					</div>

					<div class="featured-perks">
						<?php foreach ($perk_icons as $index => $icon): ?>
							<?php $key = 'featured_perk_'.($index + 1); ?>
							<?php if ($val($key) !== ''): ?>
							<div class="perk-item">
								<i class="fa-solid <?= $icon ?>"></i>
								<span <?= i18n_attr($S($key)) ?>><?= i18n_text($S($key)) ?></span>
							</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>

					<div class="featured-action">
						<a href="<?= html_escape(wa_link($wa_number, $val('featured_wa_message'))) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-accent btn-lg">
							<i class="fa-brands fa-whatsapp"></i> <span <?= i18n_attr($S('featured_btn')) ?>><?= i18n_text($S('featured_btn')) ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Packages -->
	<section class="packages-section" id="packages">
		<div class="container">
			<div class="section-header text-center">
				<span class="section-tag" <?= i18n_attr($S('packages_tag')) ?>><?= i18n_text($S('packages_tag')) ?></span>
				<h2 class="section-title" <?= i18n_attr($S('packages_title')) ?>><?= i18n_text($S('packages_title')) ?></h2>
				<p class="section-subtitle" <?= i18n_attr($S('packages_subtitle')) ?>><?= i18n_text($S('packages_subtitle')) ?></p>

				<div class="filter-tab-group" role="group" aria-label="Filter packages">
					<button type="button" class="filter-btn active" data-filter="all" <?= i18n_attr($S('filter_all')) ?>><?= i18n_text($S('filter_all')) ?></button>
					<button type="button" class="filter-btn" data-filter="diving" <?= i18n_attr($S('filter_diving')) ?>><?= i18n_text($S('filter_diving')) ?></button>
					<button type="button" class="filter-btn" data-filter="island" <?= i18n_attr($S('filter_island')) ?>><?= i18n_text($S('filter_island')) ?></button>
					<button type="button" class="filter-btn" data-filter="transport" <?= i18n_attr($S('filter_transport')) ?>><?= i18n_text($S('filter_transport')) ?></button>
				</div>
			</div>

			<div class="packages-grid" id="packagesGrid">
				<?php foreach ($packages as $package): ?>
					<?php
					$category = isset($package['category']) ? $package['category'] : 'diving';
					$cat      = isset($categories[$category]) ? $categories[$category] : $categories['diving'];
					$badge    = trim((string) $package['badge_en']);
					$style    = isset($badge_class[$package['badge_style']]) ? $badge_class[$package['badge_style']] : 'badge-popular';
					$image    = $photo($package['image']);
					$inclusions = lines_pair($package['inclusions_en'], $package['inclusions_id']);
					?>
					<article class="package-card" data-category="<?= html_escape($category) ?>">
						<div class="card-img-wrap">
							<?php if ($image !== ''): ?>
							<img src="<?= html_escape($image) ?>" alt="<?= html_escape($package['title_en']) ?>" loading="lazy" decoding="async">
							<?php endif; ?>
							<?php if ($badge !== ''): ?>
							<span class="card-badge <?= $style ?>" <?= i18n_attr(i18n_pair($package, 'badge')) ?>><?= i18n_text(i18n_pair($package, 'badge')) ?></span>
							<?php endif; ?>
							<?php if (trim((string) $package['duration_en']) !== ''): ?>
							<span class="card-duration"><i class="fa-regular fa-clock"></i> <span <?= i18n_attr(i18n_pair($package, 'duration')) ?>><?= i18n_text(i18n_pair($package, 'duration')) ?></span></span>
							<?php endif; ?>
						</div>
						<div class="card-content">
							<div class="card-meta">
								<span class="category-chip"><i class="<?= $cat['icon'] ?>"></i> <span <?= i18n_attr($S($cat['label'])) ?>><?= i18n_text($S($cat['label'])) ?></span></span>
								<span class="rating-chip"><i class="fa-solid fa-star"></i> <?= html_escape($package['rating']) ?></span>
							</div>
							<h3 class="card-title" <?= i18n_attr(i18n_pair($package, 'title')) ?>><?= i18n_text(i18n_pair($package, 'title')) ?></h3>
							<p class="card-desc" <?= i18n_attr(i18n_pair($package, 'description')) ?>><?= i18n_text(i18n_pair($package, 'description')) ?></p>

							<?php if ( ! empty($inclusions)): ?>
							<ul class="card-inclusions">
								<?php foreach ($inclusions as $line): ?>
								<li><i class="fa-solid fa-check"></i> <span <?= i18n_attr($line) ?>><?= i18n_text($line) ?></span></li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>

							<div class="card-footer">
								<div class="price-wrap">
									<span class="price-label" <?= i18n_attr(i18n_pair($package, 'price_label')) ?>><?= i18n_text(i18n_pair($package, 'price_label')) ?></span>
									<span class="price-val"><?= html_escape($package['price']) ?><small><?= html_escape($package['price_unit']) ?></small></span>
								</div>
								<a href="<?= html_escape(wa_link($wa_number, $package['wa_message'])) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">
									<span <?= i18n_attr($S('btn_book_now')) ?>><?= i18n_text($S('btn_book_now')) ?></span> <i class="fa-solid fa-angle-right"></i>
								</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Why Us -->
	<section class="why-us-section" id="whyus">
		<div class="container">
			<div class="section-header text-center">
				<span class="section-tag" <?= i18n_attr($S('why_tag')) ?>><?= i18n_text($S('why_tag')) ?></span>
				<h2 class="section-title text-white" <?= i18n_attr($S('why_title')) ?>><?= i18n_text($S('why_title')) ?></h2>
				<p class="section-subtitle text-white-80" <?= i18n_attr($S('why_subtitle')) ?>><?= i18n_text($S('why_subtitle')) ?></p>
			</div>

			<div class="why-grid">
				<?php foreach ($why_points as $point): ?>
				<div class="why-card">
					<div class="why-icon-box">
						<i class="<?= html_escape($point['icon'] !== '' ? $point['icon'] : 'fa-solid fa-star') ?>"></i>
					</div>
					<h3 class="why-card-title" <?= i18n_attr(i18n_pair($point, 'title')) ?>><?= i18n_text(i18n_pair($point, 'title')) ?></h3>
					<p class="why-card-desc" <?= i18n_attr(i18n_pair($point, 'description')) ?>><?= i18n_text(i18n_pair($point, 'description')) ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Gallery -->
	<section class="gallery-section" id="gallery">
		<div class="container">
			<div class="section-header text-center">
				<span class="section-tag" <?= i18n_attr($S('gallery_tag')) ?>><?= i18n_text($S('gallery_tag')) ?></span>
				<h2 class="section-title" <?= i18n_attr($S('gallery_title')) ?>><?= i18n_text($S('gallery_title')) ?></h2>
				<p class="section-subtitle" <?= i18n_attr($S('gallery_subtitle')) ?>><?= i18n_text($S('gallery_subtitle')) ?></p>
			</div>

			<div class="gallery-grid">
				<?php foreach ($gallery as $item): ?>
					<?php
					$is_video  = ($item['media_type'] === 'video' && trim((string) $item['video']) !== '');
					$image     = $photo($item['image']);
					$video_src = $is_video ? $photo($item['video']) : '';
					$large     = ($item['size'] === 'large');
					?>
					<figure class="gallery-item<?= $large ? ' item-large' : '' ?>"
						data-media="<?= $is_video ? 'video' : 'image' ?>"
						data-src="<?= html_escape($is_video ? $video_src : $image) ?>"
						data-caption="<?= html_escape($item['title_en']) ?>">
						<?php if ($is_video): ?>
						<video autoplay muted loop playsinline preload="metadata"<?= $image !== '' ? ' poster="'.html_escape($image).'"' : '' ?>>
							<source src="<?= html_escape($video_src) ?>" type="video/mp4">
						</video>
						<span class="gallery-play"><i class="fa-solid fa-play"></i></span>
						<?php elseif ($image !== ''): ?>
						<img src="<?= html_escape($image) ?>" alt="<?= html_escape($item['title_en']) ?>" loading="lazy" decoding="async">
						<?php endif; ?>
						<div class="gallery-overlay">
							<div class="gallery-info">
								<span class="gallery-cat" <?= i18n_attr(i18n_pair($item, 'category')) ?>><?= i18n_text(i18n_pair($item, 'category')) ?></span>
								<h4 class="gallery-name" <?= i18n_attr(i18n_pair($item, 'title')) ?>><?= i18n_text(i18n_pair($item, 'title')) ?></h4>
								<p <?= i18n_attr(i18n_pair($item, 'description')) ?>><?= i18n_text(i18n_pair($item, 'description')) ?></p>
							</div>
						</div>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Testimonials -->
	<section class="testimonials-section" id="testimonials">
		<div class="container">
			<div class="section-header text-center">
				<span class="section-tag" <?= i18n_attr($S('testi_tag')) ?>><?= i18n_text($S('testi_tag')) ?></span>
				<h2 class="section-title" <?= i18n_attr($S('testi_title')) ?>><?= i18n_text($S('testi_title')) ?></h2>
				<div class="rating-summary">
					<div class="stars">
						<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
					</div>
					<span>
						<strong <?= i18n_attr($S('testi_rating')) ?>><?= i18n_text($S('testi_rating')) ?></strong>
						<span <?= i18n_attr($S('testi_note')) ?>><?= i18n_text($S('testi_note')) ?></span>
					</span>
				</div>
			</div>

			<div class="reviews-grid">
				<?php foreach ($testimonials as $review): ?>
					<?php $stars = max(1, min(5, (int) $review['rating'])); ?>
					<div class="review-card">
						<div class="review-stars">
							<?php for ($i = 1; $i <= $stars; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
						</div>
						<p class="review-text" <?= i18n_attr(i18n_pair($review, 'quote')) ?>><?= i18n_text(i18n_pair($review, 'quote')) ?></p>
						<div class="reviewer-meta">
							<div class="avatar-circle"><?= html_escape($review['initials'] !== '' ? $review['initials'] : strtoupper(substr($review['name'], 0, 2))) ?></div>
							<div>
								<strong class="reviewer-name"><?= html_escape($review['name']) ?></strong>
								<small class="reviewer-origin"><i class="fa-solid fa-flag"></i> <span <?= i18n_attr(i18n_pair($review, 'origin')) ?>><?= i18n_text(i18n_pair($review, 'origin')) ?></span></small>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- FAQ -->
	<section class="faq-section" id="faq">
		<div class="container">
			<div class="section-header text-center">
				<span class="section-tag" <?= i18n_attr($S('faq_tag')) ?>><?= i18n_text($S('faq_tag')) ?></span>
				<h2 class="section-title" <?= i18n_attr($S('faq_title')) ?>><?= i18n_text($S('faq_title')) ?></h2>
				<p class="section-subtitle" <?= i18n_attr($S('faq_subtitle')) ?>><?= i18n_text($S('faq_subtitle')) ?></p>
			</div>

			<div class="faq-accordion-wrap">
				<?php foreach ($faqs as $faq): ?>
				<div class="faq-item">
					<button type="button" class="faq-header" aria-expanded="false">
						<span <?= i18n_attr(i18n_pair($faq, 'question')) ?>><?= i18n_text(i18n_pair($faq, 'question')) ?></span>
						<i class="fa-solid fa-chevron-down"></i>
					</button>
					<div class="faq-body">
						<p <?= i18n_attr(i18n_pair($faq, 'answer')) ?>><?= i18n_text(i18n_pair($faq, 'answer')) ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- CTA -->
	<section class="cta-banner">
		<div class="container text-center">
			<h2 class="cta-heading" <?= i18n_attr($S('cta_heading')) ?>><?= i18n_text($S('cta_heading')) ?></h2>
			<p class="cta-text" <?= i18n_attr($S('cta_text')) ?>><?= i18n_text($S('cta_text')) ?></p>
			<div class="cta-buttons">
				<a href="<?= html_escape(wa_link($wa_number, $val('cta_wa_message'))) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-accent btn-lg">
					<i class="fa-brands fa-whatsapp"></i> <span <?= i18n_attr($S('cta_btn')) ?>><?= i18n_text($S('cta_btn')) ?></span>
				</a>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="site-footer">
		<div class="container">
			<div class="footer-grid">
				<div class="footer-col col-brand">
					<div class="brand-logo footer-logo">
						<div class="logo-icon"><i class="fa-solid fa-compass"></i></div>
						<div class="logo-text">
							<span class="brand-title" <?= i18n_attr($S('brand_title')) ?>><?= i18n_text($S('brand_title')) ?></span>
							<span class="brand-subtitle" <?= i18n_attr($S('brand_subtitle')) ?>><?= i18n_text($S('brand_subtitle')) ?></span>
						</div>
					</div>
					<p class="footer-bio" <?= i18n_attr($S('footer_bio')) ?>><?= i18n_text($S('footer_bio')) ?></p>
					<div class="social-links">
						<?php if ($val('social_instagram') !== ''): ?>
						<a href="<?= html_escape($val('social_instagram')) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
						<?php endif; ?>
						<?php if ($val('social_facebook') !== ''): ?>
						<a href="<?= html_escape($val('social_facebook')) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
						<?php endif; ?>
						<?php if ($val('social_tripadvisor') !== ''): ?>
						<a href="<?= html_escape($val('social_tripadvisor')) ?>" target="_blank" rel="noopener" aria-label="Tripadvisor"><i class="fa-brands fa-tripadvisor"></i></a>
						<?php endif; ?>
						<?php if ($wa_number !== ''): ?>
						<a href="<?= html_escape($wa_base) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
						<?php endif; ?>
					</div>
				</div>

				<div class="footer-col">
					<h4 class="footer-heading" <?= i18n_attr($S('footer_h_packages')) ?>><?= i18n_text($S('footer_h_packages')) ?></h4>
					<ul class="footer-links">
						<?php foreach ($footer_packages as $link): ?>
						<li><a href="<?= html_escape($link['url']) ?>" <?= i18n_attr($link['label']) ?>><?= i18n_text($link['label']) ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="footer-col">
					<h4 class="footer-heading" <?= i18n_attr($S('footer_h_destinations')) ?>><?= i18n_text($S('footer_h_destinations')) ?></h4>
					<ul class="footer-links">
						<?php foreach ($footer_destinations as $link): ?>
						<li><a href="<?= html_escape($link['url']) ?>" <?= i18n_attr($link['label']) ?>><?= i18n_text($link['label']) ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="footer-col">
					<h4 class="footer-heading" <?= i18n_attr($S('footer_h_contact')) ?>><?= i18n_text($S('footer_h_contact')) ?></h4>
					<ul class="footer-contact-info">
						<?php if ($phone !== ''): ?>
						<li><i class="fa-solid fa-phone"></i> <a href="<?= html_escape($tel_href) ?>"><?= html_escape($phone) ?></a></li>
						<?php endif; ?>
						<?php if ($wa_number !== ''): ?>
						<li><i class="fa-brands fa-whatsapp"></i> <a href="<?= html_escape($wa_base) ?>" target="_blank" rel="noopener"><?= html_escape($phone !== '' ? $phone : '+'.$wa_number) ?></a></li>
						<?php endif; ?>
						<?php if ($val('location') !== ''): ?>
						<li><i class="fa-solid fa-location-dot"></i> <span <?= i18n_attr($S('location')) ?>><?= i18n_text($S('location')) ?></span></li>
						<?php endif; ?>
						<?php if ($val('hours') !== ''): ?>
						<li><i class="fa-solid fa-clock"></i> <span <?= i18n_attr($S('hours')) ?>><?= i18n_text($S('hours')) ?></span></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>

			<div class="footer-bottom">
				<p>&copy; <?= date('Y') ?> <span <?= i18n_attr($S('footer_copyright')) ?>><?= i18n_text($S('footer_copyright')) ?></span></p>
			</div>
		</div>
	</footer>

	<!-- Floating WhatsApp -->
	<?php if ($wa_number !== ''): ?>
	<div class="floating-wa-container">
		<a href="<?= html_escape(wa_link($wa_number, $val('wa_default_message'))) ?>" target="_blank" rel="noopener noreferrer" class="floating-wa-btn" aria-label="Chat with us on WhatsApp">
			<i class="fa-brands fa-whatsapp"></i>
			<span class="floating-wa-tooltip" <?= i18n_attr($S('wa_tooltip')) ?>><?= i18n_text($S('wa_tooltip')) ?></span>
		</a>
	</div>
	<?php endif; ?>

	<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
