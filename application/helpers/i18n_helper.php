<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper konten bilingual (EN / ID).
 *
 * Semua teks landing page disimpan sebagai pasangan EN + ID di database,
 * lalu dirender sebagai atribut data-en / data-id agar bisa diganti
 * oleh tombol bahasa di sisi klien tanpa reload.
 */

if ( ! function_exists('i18n_pair'))
{
	/**
	 * Ambil pasangan terjemahan dari satu baris tabel.
	 *
	 * @param array  $row   Baris database
	 * @param string $field Nama kolom tanpa akhiran _en / _id
	 * @return array
	 */
	function i18n_pair($row, $field)
	{
		return array(
			'en' => isset($row[$field.'_en']) ? $row[$field.'_en'] : '',
			'id' => isset($row[$field.'_id']) ? $row[$field.'_id'] : '',
		);
	}
}

if ( ! function_exists('i18n_attr'))
{
	/**
	 * Atribut HTML data-en / data-id (sudah di-escape).
	 *
	 * @param array $pair
	 * @return string
	 */
	function i18n_attr($pair)
	{
		$en = isset($pair['en']) ? (string) $pair['en'] : '';
		$id = (isset($pair['id']) && trim((string) $pair['id']) !== '') ? (string) $pair['id'] : $en;

		return 'data-en="'.html_escape($en).'" data-id="'.html_escape($id).'"';
	}
}

if ( ! function_exists('i18n_text'))
{
	/**
	 * Teks awal yang dirender server (default bahasa Inggris).
	 *
	 * @param array  $pair
	 * @param string $lang
	 * @return string
	 */
	function i18n_text($pair, $lang = 'en')
	{
		$value = isset($pair[$lang]) ? (string) $pair[$lang] : '';
		if (trim($value) === '' && isset($pair['en']))
		{
			$value = (string) $pair['en'];
		}

		return html_escape($value);
	}
}

if ( ! function_exists('media_url'))
{
	/**
	 * Ubah path media relatif menjadi URL absolut.
	 * URL eksternal (http/https) dibiarkan apa adanya.
	 *
	 * @param string $path
	 * @return string
	 */
	function media_url($path)
	{
		$path = trim((string) $path);

		if ($path === '')
		{
			return '';
		}

		if (preg_match('#^(https?:)?//#i', $path) === 1)
		{
			return $path;
		}

		return base_url(str_replace(' ', '%20', ltrim($path, '/')));
	}
}

if ( ! function_exists('text_lines'))
{
	/**
	 * Pecah textarea menjadi array baris (baris kosong dibuang).
	 *
	 * @param string $text
	 * @return array
	 */
	function text_lines($text)
	{
		$lines = preg_split('/\r\n|\r|\n/', (string) $text);
		$lines = array_map('trim', $lines);

		return array_values(array_filter($lines, function ($line) {
			return $line !== '';
		}));
	}
}

if ( ! function_exists('lines_pair'))
{
	/**
	 * Pasangkan daftar baris EN & ID berdasarkan urutan baris.
	 *
	 * @param string $en
	 * @param string $id
	 * @return array
	 */
	function lines_pair($en, $id)
	{
		$en_lines = text_lines($en);
		$id_lines = text_lines($id);
		$out = array();

		foreach ($en_lines as $index => $line)
		{
			$out[] = array(
				'en' => $line,
				'id' => (isset($id_lines[$index]) && $id_lines[$index] !== '') ? $id_lines[$index] : $line,
			);
		}

		return $out;
	}
}

if ( ! function_exists('wa_link'))
{
	/**
	 * Bangun tautan WhatsApp (wa.me) dari nomor internasional.
	 *
	 * @param string $number  Contoh: 6283834473710
	 * @param string $message
	 * @return string
	 */
	function wa_link($number, $message = '')
	{
		$number = preg_replace('/[^0-9]/', '', (string) $number);
		$url = 'https://wa.me/'.$number;

		if (trim((string) $message) !== '')
		{
			$url .= '?text='.rawurlencode($message);
		}

		return $url;
	}
}

if ( ! function_exists('teaser'))
{
	/**
	 * Potong teks panjang untuk meta description / preview.
	 *
	 * @param string $text
	 * @param int    $limit
	 * @return string
	 */
	function teaser($text, $limit = 160)
	{
		$text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));

		if (function_exists('mb_strlen') && mb_strlen($text) > $limit)
		{
			return mb_substr($text, 0, $limit - 1).'…';
		}

		return strlen($text) > $limit ? substr($text, 0, $limit - 1).'…' : $text;
	}
}
