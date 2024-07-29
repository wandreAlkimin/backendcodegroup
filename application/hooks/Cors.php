<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cors {

	public function __construct() {
		// Constructor code here
	}

	public function enableCors() {
		$CI =& get_instance();
		$CI->output
			->set_header('Access-Control-Allow-Origin: *')
			->set_header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE')
			->set_header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

		// Handle preflight request
		if ($CI->input->method() == 'options') {
			$CI->output
				->set_status_header(200)
				->_display();
			exit;
		}
	}
}
