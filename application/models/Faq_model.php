<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'models/Content_model.php';

/**
 * FAQ (bilingual).
 */
class Faq_model extends Content_model
{
	protected $table = 'faqs';
}
