<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| KONFIGURASI SEO TERPUSAT ($config['seo'] diakses via item('seo'))
|--------------------------------------------------------------------------
| Nilai di file ini adalah fallback terakhir. Data dari tabel `settings`
| (diisi lewat admin) selalu diprioritaskan oleh Seo_lib.
|
| canonical_base : domain kanonik (tanpa trailing slash, tanpa www)
| search_url     : target Sitelinks Searchbox (harus benar-benar berfungsi)
| brand          : data Organization / TravelAgency untuk JSON-LD
| sitemap        : pengaturan generator sitemap.xml
| robots         : aturan robots.txt versi controller
|
| Pemakaian: $this->config->load('seo', TRUE);
|            $seo = $this->config->item('seo');
*/

$config['canonical_base'] = 'https://nusatripper.com';

$config['locale']     = 'en_US';
$config['locale_alt'] = 'id_ID';

// Kosongkan bila akun X/Twitter belum ada.
$config['twitter_site'] = '';

// Target Sitelinks Searchbox. Route `paket` memetakan ke Home::index yang
// membaca parameter `q` (filter paket), jadi URL ini benar-benar berfungsi.
$config['search_url'] = 'https://nusatripper.com/paket?q={search_term_string}';

$config['brand'] = array(
	'name'      => 'Nusa Tripper',
	'legal'     => 'Nusa Tripper',
	'logo'      => 'assets/images/logo.png',
	'phone'     => '',
	'email'     => '',
	'street'    => '',
	'city'      => 'Lombok',
	'region'    => 'Nusa Tenggara Barat',
	'postal'    => '',
	'country'   => 'ID',
	'latitude'  => '-8.5831',
	'longitude' => '116.1167',
	'same_as'   => array(),
);

$config['sitemap'] = array(
	'cache_minutes' => 720,
	'image_limit'   => 20,

	/*
	 * Sumber tambahan berbasis database. Tabel hanya dipakai jika memiliki
	 * kolom `slug` (opsional `updated_at`). Contoh saat blog tersedia:
	 *
	 * array('table' => 'articles', 'prefix' => 'blog/', 'changefreq' => 'weekly', 'priority' => '0.7'),
	 */
	'sources' => array(),
);

$config['robots'] = array(
	'disallow' => array(
		'/admin/',
		'/application/',
		'/system/',
	),
	'allow' => array(
		'/assets/css/',
		'/assets/js/',
		'/assets/images/',
		'/uploads/',
	),
);