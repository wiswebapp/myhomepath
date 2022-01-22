<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

	public $tconfig;

	public function __construct()
	{
		parent::__construct();
		is_adminlogged_in();
		$this->load->model('Users_model');
		$this->load->model('Common_model');
		$this->load->library('upload');
		$this->load->helper('Adminform_helper');

		$this->tconfig = $this->tconfig();
	}

	public function index(){
		return redirect(admin_url('users/list'));
	}
	/*==========================REGISTER USERS LIST==========================*/
	public function list(){

		$ssql = "userType = 'User' AND deleted_at IS NULL";
		if(isset($_GET['name']) && ! empty(trim($_GET['name']))){
    		$ssql .= " AND name LIKE '%".trim($_GET['name'])."%' ";
    	}
    	if(isset($_GET['mobile']) && ! empty(trim($_GET['mobile']))){
    		$ssql .= " AND phone LIKE '%".trim($_GET['mobile'])."%' ";
    	}
    	if(isset($_GET['email']) && ! empty(trim($_GET['email']))){
    		$ssql .= " AND email LIKE '%".trim($_GET['email'])."%' ";
    	}
        if(isset($_GET['status']) && ! empty(trim($_GET['status']))){
            $ssql .= " AND isActive = '".trim($_GET['status'])."' ";
        }
        
		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		
		$totalAllData = $this->Users_model->__getdatacount('med_users',$ssql);
		$data['usersList'] = $this->Users_model->getRegisterUsers('*', $offset, $this->dataLimit, $ssql);
		$data['usersList']['totalAllData'] = $totalAllData;
		//where while searching
        $config = $this->configPagination(
        	admin_url('users/list'),$totalAllData,$this->dataLimit,4)
        ;
		$this->pagination->initialize($config);
		$this->load->view('register_users',compact('data'));
	}
	public function add(){
		$post = $this->input->post();
		$data['action'] = 'add';
		$data['country'] = $this->Users_model->__getwheredata('country','iCountryId,vCountry,vCountryCode,vPhoneCode',array('eStatus'=>'Active'),'vCountry','asc',NULL);
		if(!empty($post)){

			$chk = $this->Users_model->checkDuplicate('register_user','vEmail',$post['vEmail']);
			if($this->form_validation->run('checkRegUser') && $chk == 0) {
				//Image Upload
				if(!empty($_FILES['vImgName']['name'])){
					$pathToUpload = $this->tconfig['root_path'].'webimages/user/';
					$upload = $this->_solo_file_upload('vImgName',$pathToUpload);
					$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
					$post['vImgName'] = $fileName;
				}

				//Password Change
				if(!empty($post['vPassword'])){
					$post['vPassword'] = encryptPassword($post['vPassword']);
				}else{
					unset($post['vPassword']);
				}
				
				unset($post['oldvImage']);
				$post['tRegistrationDate'] = date('Y-m-d');
				$post['vPhoneCode'] = date('Y-m-d');
				
				$add = $this->Users_model->__add_single_data('register_user',$post);
				
				$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('users/list') );
			}else{
				$this->__setflash( FALSE, '', 'Email Is already Exist');
				$this->load->view('register_users_action',compact('data'));
			}
		}else{
			$this->load->view('register_users_action',compact('data'));
		}
	}
	public function edit($userId=''){
		if(!empty($userId)){
			$post = $this->input->post();
			if(empty($post)){
				$where = array('iUserId'=>$userId);
				$data['action'] = 'edit';
				$data['country'] = $this->Users_model->__getwheredata('country','iCountryId,vCountry,vCountryCode,vPhoneCode',array('eStatus'=>'Active'),'vCountry','asc',NULL);
				$data['usersData'] = $this->Users_model->__getsingledata('register_user','*',$where);
				$this->load->view('register_users_action',compact('data'));
			}else{
				//Image Upload
				if(!empty($_FILES['vImgName']['name'])){
					$pathToUpload = $this->tconfig['root_path'].'webimages/user/';
					//unlink old image
					unlink($pathToUpload.$post['oldvImage']);
					$upload = $this->_solo_file_upload('vImgName',$pathToUpload);
					$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
					$post['vImgName'] = $fileName;
				}
				//Password Change
				if(!empty($post['vPassword'])){
					$post['vPassword'] = encryptPassword($post['vPassword']);
				}else{
					unset($post['vPassword']);
				}

				$condition = array('iUserId'=>$userId);
				$chk = $this->Users_model->checkDuplicate('register_user','vEmail',$post['vEmail'],'iUserId !='.$userId);
				if($this->form_validation->run('checkRegUser') && $chk == 0) {
					unset($post['oldvImage']);
					$upadteUser = $this->Users_model->__update_single_data('register_user',$post,$condition);
					$this->__setflash( $upadteUser, 'Data Updated Successfully .!', 'Error while updating', admin_url('users/list') );
				}else{
					$this->load->view('register_users_action',compact('data'));
				}
			}
		}else{
			return redirect(admin_url('users/list'));
		}
	}

	/*==========================ADMIN USERS LIST==========================*/
	public function admin(){

		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data['usersList'] = $this->Users_model->getAdminUsers('*',$offset,$this->dataLimit);

		//where while searching
		$ssql = " eStatus != 'Deleted'";
		if(!empty(trim($_GET['name']))){
    		$ssql .= " AND (vFirstName LIKE '%".trim($_GET['name'])."%' OR vLastName LIKE '%".trim($_GET['name'])."%') ";
    	}
    	if(!empty(trim($_GET['email']))){
    		$ssql .= " AND vEmail LIKE '%".trim($_GET['email'])."%' ";
    	}
    	if(!empty(trim($_GET['role']))){
    		$ssql .= " AND iGroupId LIKE '%".trim($_GET['role'])."%' ";
    	}
        if(!empty(trim($_GET['status']))){
            $ssql .= " AND eStatus = '".trim($_GET['status'])."' ";
        }

        $config = $this->configPagination(
        	admin_url('users/admin'),
        	$this->Users_model->__getdatacount('administrators',$ssql),
        	$this->dataLimit,4
        );
		$this->pagination->initialize($config);
		$this->load->view('admin_users',compact('data'));
	}
	public function add_admin(){
		$post = $this->input->post();
		$data['action'] = 'add';
		$data['group'] = $this->Users_model->__getwheredata('admin_groups','*',array('eStatus'=>'Active'),'iGroupId',NULL,NULL);
		if(!empty($post)){
			$chk = $this->Users_model->checkDuplicate('administrators','vEmail',$post['vEmail']);
			if($chk == 0) {
				//Password
				if(!empty($post['vPassword'])){
					$post['vPassword'] = encryptPassword($post['vPassword']);
				}else{
					unset($post['vPassword']);
				}
				$add = $this->Users_model->__add_single_data('administrators',$post);
				$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('users/admin') );
			}else{
				$this->load->view('admin_users_action',compact('data'));
			}			
		}else{
			$this->load->view('admin_users_action',compact('data'));
		}
	}
	public function edit_admin($userId=''){
		if(!empty($userId)){
			$post = $this->input->post();
			if(empty($post)){
				$where = array('iAdminId'=>$userId);
				$data['action'] = 'edit';
				$data['group'] = $this->Users_model->__getwheredata('admin_groups','*',array('eStatus'=>'Active'),'iGroupId',NULL,NULL);
				$data['usersData'] = $this->Users_model->__getsingledata('administrators','*',$where);
				$this->load->view('admin_users_action',compact('data'));
			}else{
				//Password Change
				if(!empty($post['vPassword'])){
					$post['vPassword'] = encryptPassword($post['vPassword']);
				}else{
					unset($post['vPassword']);
				}
				$condition = array('iAdminId'=>$userId);
				$chk = $this->Users_model->checkDuplicate('administrators','vEmail',$post['vEmail'],'iAdminId !='.$userId);
				if($chk == 0){
					$upadteUser = $this->Users_model->__update_single_data('administrators',$post,$condition);
				}
				$this->__setflash( $upadteUser, 'Data Updated Successfully .!', 'Error while updating', admin_url('users/admin') );
			}
		}else{
			return redirect(admin_url('users/admin'));
		}
	}

	/*==========================ORGANIZATIONS USERS LIST==========================*/
	public function organization(){

		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

		//where while searching
		$ssql = "eStatus != 'Deleted'";
		if(!empty(trim($_GET['name']))){
    		$ssql .= " AND vOrgName LIKE '%".trim($_GET['name'])."%'";
    	}
    	if(!empty(trim($_GET['email']))){
    		$ssql .= " AND vUserEmail LIKE '%".trim($_GET['email'])."%' ";
    	}
    	if(!empty(trim($_GET['mobile']))){
    		$ssql .= " AND vUserMobile LIKE '%".trim($_GET['mobile'])."%' ";
    	}
        if(!empty(trim($_GET['status']))){
            $ssql .= " AND eStatus = '".trim($_GET['status'])."' ";
        }
        $data['pagedata'] = $this->Users_model->getOrganizations('*', $offset, $this->dataLimit, $ssql);
       	
        $config = $this->configPagination(
        	admin_url('users/organization'),
        	$this->Users_model->__getdatacount('organization',$ssql),
        	$this->dataLimit,4
        );
		$this->pagination->initialize($config);
		$this->load->view('register_organization',compact('data'));
	}
	public function add_organization(){
		$post = $this->input->post();
		$data['action'] = 'add';
		$data['country'] = $this->Users_model->getCountryList();
		$data['state'] = $this->Users_model->getStateList();
		$data['city'] = $this->Users_model->getCityList();
		if(!empty($post)){
			/*$chk = $this->Users_model->checkDuplicate('organization','vUserEmail',$post['vUserEmail']);
			$chk = $this->Users_model->checkDuplicate('organization','vUserMobile',$post['vUserMobile']);*/
			$chk = $this->Users_model->checkDuplicate('organization','vUserEmail',$post['vUserEmail'],'iOrgId !='.$userId);
			if(!empty($chk)){
				$this->__setflash( FALSE, '', 'Email is already Exist .!');
				$this->load->view('register_organization_action',compact('data'));	
			}
			$chk = $this->Users_model->checkDuplicate('organization','vUserMobile',$post['vUserMobile'],'iOrgId !='.$userId);
			if(!empty($chk)){
				$this->__setflash( FALSE, '', 'Mobile is already Exist .!');
				$this->load->view('register_organization_action',compact('data'));	
			}
			if($this->form_validation->run('checkOrganization') && $chk == 0) {
				$post['tRegDate'] = date('Y-m-d');
				//Image Upload
				if(!empty($_FILES['vLogoImg']['name'])){
					$pathToUpload = $this->tconfig['root_path'].'webimages/organization/';
					$customFilename = "web_organization_".time();
					$upload = $this->_solo_file_upload('vLogoImg',$pathToUpload,'',$customFilename);
					if($upload['success'] == 1){
						$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
						$post['vLogoImg'] = $fileName;	
					}
				}
				//Password Change
				if(!empty($post['vPassword'])){
					$post['vPassword'] = encryptPassword($post['vPassword']);
				}else{
					unset($post['vPassword']);
				}
				$add = $this->Users_model->__add_single_data('organization',$post);
				$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('users/organization') );
			}else{
				$this->__setflash( $add, '', 'Email is already Exist !');
				$this->load->view('register_organization_action',compact('data'));
			}
		}else{
			$this->load->view('register_organization_action',compact('data'));
		}
	}
	public function edit_organization($userId=''){
		if(!empty($userId)){
			$post = $this->input->post();
			$data['action'] = 'edit';
			$data['country'] = $this->Users_model->getCountryList();
			$data['state'] = $this->Users_model->getStateList();
			$data['city'] = $this->Users_model->getCityList();
			$where = array('iOrgId'=>$userId);
			if(empty($post)){
				$data['usersData'] = $this->Users_model->__getsingledata('organization','*',$where);
				$this->load->view('register_organization_action',compact('data'));
			}else{
				//Password Change
				if(!empty($post['vPassword'])){
					$post['vPassword'] = encryptPassword($post['vPassword']);
				}else{
					unset($post['vPassword']);
				}
				//Image Upload
				if(!empty($_FILES['vLogoImg']['name'])){
					$pathToUpload = $this->tconfig['root_path'].'webimages/organization/';
					//unlink old image
					unlink($pathToUpload.$post['oldvImage']);
					unset($post['oldvImage']);
					$customFilename = "web_organization_".time();
					$upload = $this->_solo_file_upload('vLogoImg',$pathToUpload,'',$customFilename);
					$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
					$post['vLogoImg'] = $fileName;
				}else{
					unset($post['oldvImage']);
				}

				$chk = $this->Users_model->checkDuplicate('organization','vUserEmail',$post['vUserEmail'],'iOrgId !='.$userId);
				if(!empty($chk)){
					$this->__setflash( FALSE, '', 'Email is already Exist .!');
					$this->load->view('register_organization_action',compact('data'));	
				}
				$chk = $this->Users_model->checkDuplicate('organization','vUserMobile',$post['vUserMobile'],'iOrgId !='.$userId);
				if(!empty($chk)){
					$this->__setflash( FALSE, '', 'Mobile is already Exist .!');
					$this->load->view('register_organization_action',compact('data'));	
				}

				if($this->form_validation->run('checkOrganization')) {
					$upadteUser = $this->Users_model->__update_single_data('organization',$post,$where);
					$this->__setflash( TRUE, 'Data Updated Successfully .!', 'Error while updating', admin_url('users/organization') );
				}else{
					$this->load->view('register_organization_action',compact('data'));
				}
			}
		}else{
			return redirect(admin_url('users/organization'));
		}
	}

	/*==========================GLOBAL CONTROLLER FUNCTION==========================*/
	public function changeStatus(){
		
		$userId = $this->input->post('userid');
		$type = $this->input->post('actionType');
		$actionTable = $this->input->post('actionTable');
			
		$key = 'iAdminId';
		if($actionTable == 'register_user'){ $key = 'iUserId'; }
		if($actionTable == 'register_agent'){ $key = 'iAgentId';}
		if($actionTable == 'organization'){ $key = 'iOrgId';}
		$data = array('eStatus' => $type);
		$condition = array($key => $userId);
		$status = $this->Users_model->__update_single_data($actionTable, $data, $condition);
		echo $status;
	}
	public function changeBulkStatus(){
		$userId = $this->input->post('bulkid');
		$type = $this->input->post('type');
		$actionTable = $this->input->post('table');

		if(!empty($userId)){
			$key = 'iUserId';
			if($actionTable == 'register_user'){ $key = 'iUserId'; }
			if($actionTable == 'register_agent'){ $key = 'iAgentId';}
			if($actionTable == 'organization'){ $key = 'iOrgId';}
			$data = array('eStatus' => $type);
			$status = $this->Users_model->__update_multiple_data($actionTable, $data, $key, $userId);
			echo $status;
		}
	}
}
