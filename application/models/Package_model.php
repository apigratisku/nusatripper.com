<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'models/Content_model.php';

/**
 * Paket wisata.
 */
class Package_model extends Content_model
{
	protected $table = 'packages';
}
