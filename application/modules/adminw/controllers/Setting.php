<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends MY_Controller {

	public function __construct(){
		parent::__construct();
		is_adminlogged_in();
	}

	public function index(){
		$data['pagedata'] = $this->Setting_model->getSettingData();
		$this->load->view('setting',compact('data'));
	}
	public function add_configuration(){
		$post = $this->input->post();
		if(!empty( $post) ){
			$msg = $this->SettingModel->__add_single_data('configurations',$post);
			$this->__setflash( $msg, 'Settings Has Been Added Successfully', 'Error', admin_url('setting/') );
		}else{
			$this->load->view('settings_action.php');
		}
	}
	public function save($tabName){
		$post = $this->input->post();
		$tabName = str_replace('%20', ' ', $tabName);
		if(!empty($post) && !empty($tabName)){
			
			//$this->SettingModel->updateSettings($tabName,$post);
			$msg = $this->Setting_model->updateSettings($tabName,$post);
			$this->session->set_flashdata('activeTab',$tabName);
			$this->__setflash( $msg, $tabName.' Settings Has Been Updated Successfully', 'Error', admin_url('setting/') );

		}else{
			redirect(admin_url('setting/'));
		}
	}

}


?>