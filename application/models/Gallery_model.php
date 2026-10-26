<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'models/Content_model.php';

/**
 * Item galeri (foto / video).
 */
class Gallery_model extends Content_model
{
	protected $table = 'gallery';
}
