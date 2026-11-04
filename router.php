<?php
// Router untuk PHP built-in server: handle asset & media statis dengan URL ter-encode,
// fallback ke CodeIgniter index.php untuk rute aplikasi.

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$docroot = __DIR__;

// Cek apakah file statis ada (termasuk nama dengan spasi).
if ($uri !== '/' && is_file($docroot . $uri))
{
	return false; // Biarkan PHP server menyajikan file apa adanya.
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require $docroot . '/index.php';
