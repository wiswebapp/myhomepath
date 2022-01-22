<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {
    
	public function __construct(){
		parent::__construct();
		$this->load->helper('form');
        $this->load->model('Auth_model');
        is_adminsignin_loggedin();
	}

    public function index(){
        $this->load->view('login');
    }

    public function getAdminGroupName($adminGroupId){
        $where = 'iGroupId = '.$adminGroupId;
        return $this->Auth_model->__getsingledata('admin_groups','vGroup',$where);
    }

    public function auth()
    {
        $post = $this->input->post();
        if(!empty($post)){
            $uname = $this->input->post('username');
            $pass = $this->input->post('password');
            if($chk = $this->form_validation->run('checkAdminLogin')) {

				$authenticate = $this->Auth_model->isAdminValid($uname,$pass);
                
				if($authenticate['Action'] == 'TRUE'){
                    $this->session->set_userdata('iAdminUserId',base64_encode($authenticate[0]['id']));
                    return redirect(admin_url('dashboard'));
                }else{
                    $this->__setflash(FALSE,'','Invalid Credentials Entered .!','adminw/login');
                }
            }else{
                $this->load->view('login');
            }
        }else{
            redirect(admin_url());
        }
    }
}
