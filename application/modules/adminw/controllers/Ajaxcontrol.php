<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajaxcontrol extends MY_Controller {

	public function __construct(){
		parent::__construct();
		is_adminlogged_in();
		$this->load->model('Master_model');
	}

}