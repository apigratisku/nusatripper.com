<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper aset gambar untuk performa (Core Web Vitals).
 *
 * - webp_url() : prioritaskan versi WebP bila file .webp tersedia.
 * - img_tag()  : <img> siap pakai dengan lazy loading, decoding async,
 *                serta atribut width/height eksplisit (mencegah CLS).
 */

if ( ! function_exists('webp_url'))
{
	/**
	 * Ubah path gambar ke versi WebP jika file WebP-nya ada.
	 * URL eksternal (http/https) dibiarkan apa adanya.
	 *
	 * @param string $path
	 * @return string
	 */
	function webp_url($path)
	{
		$path = trim((string) $path);

		if ($path === '')
		{
			return '';
		}

		if (preg_match('#^https?://#i', $path) === 1)
		{
			return $path;
		}

		$relative = ltrim(str_replace(' ', '%20', $path), '/');
		$webp     = preg_replace('/\.(jpe?g|png)$/i', '.webp', $relative);

		if ($webp !== $relative && is_file(FCPATH.$webp))
		{
			$relative = $webp;
		}

		return media_url($relative);
	}
}

if ( ! function_exists('img_tag'))
{
	/**
	 * Tag <img> optimasi: WebP (jika ada), lazy loading, width/height
	 * eksplisit, decoding async.
	 *
	 * @param string $path    Path media relatif atau URL absolut
	 * @param string $alt     Teks alternatif
	 * @param int    $width   Lebar intrinsik (px)
	 * @param int    $height  Tinggi intrinsik (px)
	 * @param string $attr    Atribut tambahan, mis. ' class="card-img"'
	 * @param bool   $lazy    FALSE untuk gambar above-the-fold (LCP)
	 * @return string
	 */
	function img_tag($path, $alt = '', $width = 0, $height = 0, $attr = '', $lazy = TRUE)
	{
		$src = webp_url($path);

		if ($src === '')
		{
			return '';
		}

		$html = '<img src="'.html_escape($src).'" alt="'.html_escape($alt).'"';

		if ($width > 0)
		{
			$html .= ' width="'.(int) $width.'"';
		}

		if ($height > 0)
		{
			$html .= ' height="'.(int) $height.'"';
		}

		if ($lazy)
		{
			$html .= ' loading="lazy"';
		}
		else
		{
			$html .= ' loading="eager" fetchpriority="high"';
		}

		$html .= ' decoding="async"'.$attr.'>';

		return $html;
	}
}