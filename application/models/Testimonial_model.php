<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'models/Content_model.php';

/**
 * Testimoni pelanggan.
 */
class Testimonial_model extends Content_model
{
	protected $table = 'testimonials';
}
