<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $tconfig = $this->tconfig();

        $this->load->model('Common_front_model');
        $this->load->model('Search_model');
        $this->load->library('Recaptcha');
    }

    public function index(){
        $this->load->view('index');
    }
}
?>
