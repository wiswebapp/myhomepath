<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct(){
        parent::__construct();
        is_adminlogged_in();
        $this->load->model('Common_model');
    }

    public function index()
    {
        $journeyDate = date('Y-m-d');
        $dashboard['tconfig'] = $this->tconfig();

		$userCnt = ['userType' => 'User','isActive' => 'Yes'];
        $data['totalCustomer'] = $this->Common_model->__getdatacount('med_users', $userCnt);
		
		$catCnt = ['status' => 'Active', 'parent_Id' => 0];
        $data['totalCategory'] = $this->Common_model->__getdatacount('categories', $catCnt);
		
		$proCnt = ['status' => 'Active', 'user_id' => 1];
        $data['totalProduct'] = $this->Common_model->__getdatacount('products', $proCnt);

        $this->load->view('dashboard',compact('data'));
    }
    public function logout(){
        session_destroy();
        return redirect(admin_url('login'));
        exit;
    }
}
