<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends MY_Controller {

	public function __construct(){
		parent::__construct();
		is_adminlogged_in();
		$this->load->model('Master_model');
	}

	public function index(){
		return redirect(admin_url());
	}

	/*==========================CATEGORY LIST==========================*/
	public function category(){
		$ssql = "parent_id = 0";
		if(isset($_GET['name']) && ! empty(trim($_GET['name']))){
    		$ssql .= " AND category_name LIKE '%".trim($_GET['name'])."%' ";
    	}
    	if(isset($_GET['status']) && ! empty(trim($_GET['status']))){
            $ssql .= " AND status = '".trim($_GET['status'])."' ";
        }

		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$countData = $this->Master_model->__getdatacount('categories', $ssql);
		$data['pagedata']['data'] = $this->Master_model->getServiceCategory($ssql,$this->dataLimit,$offset);
		$data['pagedata']['count'] = $countData;
		$data['pagedata']['tconfig'] = $this->tconfig();
		$config = $this->configPagination(admin_url('master/category'),$countData,$this->dataLimit,4);
		$this->pagination->initialize($config);
		
		$this->load->view('category',compact('data'));
	}

	public function add_category(){
		$post = $this->input->post();
		$data['action'] = 'add';
		$data['tconfig'] = $this->tconfig();
		
		if(!empty($post)){
			//Image Upload
			if(!empty($_FILES['vLogo']['name'])){
				$pathToUpload = $this->tconfig['root_path'].'webimages/category/';
				$generatedFileName = "web_category_".time();
				$upload = $this->_solo_file_upload('vLogo',$pathToUpload,'',$generatedFileName);
				$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
				$post['vLogo'] = $fileName;
			}
			$add = $this->Master_model->__add_single_data('categories',$post);
			$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('master/category') );
		}else{
			$this->load->view('category_action',compact('data'));
		}
	}

	public function edit_category($id){
		if(empty($id)){
			return redirect(admin_url('master/category'));
		}
		
		$post = $this->input->post();
		$data['tconfig'] = $this->tconfig();
		$where = array('id' => $id);
		
		if(empty($post)){
			$data['action'] = 'edit';
			$data['pageData'] = $this->Master_model->__getsingledata('categories','*',$where);
			$this->load->view('category_action',compact('data'));
		}else{
			//Image Upload
			if(!empty($_FILES['vLogo']['name'])){
				$pathToUpload = $this->tconfig['root_path'].'webimages/category/';
				//unlink old image
				unlink($pathToUpload.$post['oldvLogo']);
				unset($post['oldvLogo']);
				$generatedFileName = "web_category_".time();
				$upload = $this->_solo_file_upload('vLogo', $pathToUpload, '', $generatedFileName);
				$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
				$post['vLogo'] = $fileName;
			}else{
				unset($post['oldvLogo']);
			}

			$update = $this->Master_model->__update_single_data('categories',$post,$where);
			$this->__setflash( $update, 'Data Updated Successfully .!', 'Error while updating', admin_url('master/category') );
		}
	}

	/*==========================PRODUCT LIST==========================*/
	public function product(){
		$ssql = "deleted_at IS NULL";
		if(isset($_GET['name']) && ! empty(trim($_GET['name']))){
    		$ssql .= " AND product_name LIKE '%".trim($_GET['name'])."%' ";
    	}
    	if(isset($_GET['status']) &&! empty(trim($_GET['status']))){
            $ssql .= " AND status = '".trim($_GET['status'])."' ";
        }

		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$countData = $this->Master_model->__getdatacount('products',$ssql);
		$data['pagedata']['data'] = $this->Master_model->getProductData($ssql,$this->dataLimit,$offset);
		$data['pagedata']['count'] = $countData;
		$data['pagedata']['tconfig'] = $this->tconfig();
		$config = $this->configPagination(admin_url('master/product'),$countData,$this->dataLimit,4);
		$this->pagination->initialize($config);
		
		$this->load->view('product',compact('data'));
	}

	public function add_product() {
		$post = $this->input->post();
		$data['action'] = 'add';
		$data['tconfig'] = $this->tconfig();
		$ssqlCat = "status = 'Active'";
		$data['category'] = $this->Master_model->getServiceCategory($ssqlCat);
		unset($post['oldvLogo']);
		if(!empty($post)){
			//Image Upload
			if(!empty($_FILES['product_image']['name'])){
				$pathToUpload = $this->tconfig['root_path'].'webimages/productImage/';
				$generatedFileName = "web_product_".time();
				$upload = $this->_solo_file_upload('product_image',$pathToUpload,'',$generatedFileName);
				if($upload['success']){
					$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
					$post['product_image'] = $fileName;
				}
			}
			$add = $this->Master_model->__add_single_data('products',$post);
			$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('master/product') );
		}else{
			$this->load->view('product_action',compact('data'));
		}
	}

	public function edit_product($id) {
		if(empty($id)) return redirect(admin_url('master/product'));
		
		$data['tconfig'] = $this->tconfig();
		$ssqlCat = "status = 'Active'";
		$data['category'] = $this->Master_model->getServiceCategory($ssqlCat);
	
		$post = $this->input->post();
		$ssqlCat = "status = 'Active'";
		$data['product'] = $this->Master_model->getProductData($ssqlCat);
		$where = array('id'=>$id);
		if(empty($post)){
			$data['action'] = 'edit';
			$data['pageData'] = $this->Master_model->__getsingledata('products','*',$where);
			$this->load->view('product_action',compact('data'));
		}else{
			//Image Upload
			
			if(! empty($_FILES['product_image']['name'])){
				$pathToUpload = $data['tconfig']['root_path'].'webimages/productImage/';
				
				//unlink old image
				if(! empty($post['oldvLogo'])) {
					unlink($pathToUpload.$post['oldvLogo']);
				}
				unset($post['oldvLogo']);
				$generatedFileName = "web_product_".time();
				$upload = $this->_solo_file_upload('product_image', $pathToUpload, '', $generatedFileName);
				if($upload['success']){
					$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
					$post['product_image'] = $fileName;
				}
			}else{
				unset($post['oldvLogo']);
			}

			$update = $this->Master_model->__update_single_data('products',$post,$where);
			$this->__setflash( $update, 'Data Updated Successfully .!', 'Error while updating', admin_url('master/product') );
		}
	}
	/*==========================SUBSCRIPTION PLAN LIST==========================*/
	public function subscription(){
		$ssql = "1 = 1 ";
		if(!empty(trim($_GET['name']))){
    		$ssql .= " AND vPlanName LIKE '%".trim($_GET['name'])."%' ";
    	}
    	if(!empty(trim($_GET['status']))){
            $ssql .= " AND eStatus = '".trim($_GET['status'])."' ";
        }

		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$countData = $this->Master_model->__getdatacount('subscription_plan',$ssql);
		$data['pagedata']['data'] = $this->Master_model->getSubscriptionPlanMyModl($ssql,$this->dataLimit,$offset);
		$data['pagedata']['count'] = $countData;
		$data['pagedata']['tconfig'] = $this->tconfig();
		$config = $this->configPagination(admin_url('master/subscription'),$countData,$this->dataLimit,4);
		$this->pagination->initialize($config);

		$this->load->view('subscription_plan',compact('data'));
	}
	public function add_subscription(){
		$post = $this->input->post();
		$data['action'] = 'add';
		$data['tconfig'] = $this->tconfig();
		$ssql = "eStatus = 'Active' ";
		$data['productData'] = $this->Master_model->getProductData($ssql);
		if(!empty($post)){
			$ssqlGetCat = 'iProductId = '.$post['iProductId'];
			$getCat = $this->Master_model->getProductData($ssqlGetCat);
			$post['iCategoryId'] = $getCat[0]['iCategoryId'];
			$add = $this->Master_model->__add_single_data('subscription_plan',$post);
			$this->__setflash( $add, 'Data Added Successfully .!', 'Error', admin_url('master/subscription') );
		}else{
			$this->load->view('subscription_plan_action',compact('data'));
		}
	}
	public function edit_subscription($id){
		if(!empty($id)){
			$post = $this->input->post();
			$data['tconfig'] = $this->tconfig();
			$ssqlPro = "eStatus = 'Active' ";
			$data['productData'] = $this->Master_model->getProductData($ssqlPro);
			$where = array('iPlanId'=>$id);
			if(empty($post)){
				$data['action'] = 'edit';
				$data['pageData'] = $this->Master_model->__getsingledata('subscription_plan','*',$where);
				$this->load->view('subscription_plan_action',compact('data'));
			}else{
				$ssqlGetCat = 'iProductId = '.$post['iProductId'];
				$getCat = $this->Master_model->getProductData($ssqlGetCat);
				$post['iCategoryId'] = $getCat[0]['iCategoryId'];
				$update = $this->Master_model->__update_single_data('subscription_plan',$post,$where);
				$this->__setflash( $update, 'Data Updated Successfully .!', 'Error while updating', admin_url('master/subscription') );
			}
		}else{
			return redirect(admin_url('master/subscription'));
		}
	}
	/*==========================COUNTRY LIST==========================*/
	public function country(){
		$ssql = " 1 = 1";
		if(!empty(trim($_GET['name']))){
    		$ssql .= " AND vCountry LIKE '%".trim($_GET['name'])."%' ";
    	}
    	if(!empty(trim($_GET['code']))){
    		$ssql .= " AND vCountryCode LIKE '%".trim($_GET['code'])."%' ";
    	}
        if(!empty(trim($_GET['status']))){
            $ssql .= " AND eStatus = '".trim($_GET['status'])."' ";
        }

		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data['country'] = $this->Master_model->__getwheredata('country','*',$ssql,'iCountryId','ASC',$this->dataLimit,$offset);

		$config = $this->configPagination(
        	admin_url('master/country'),
        	$this->Master_model->__getdatacount('country',$ssql),
        	$this->dataLimit,4
        );
		$this->pagination->initialize($config);

		$this->load->view('country',compact('data'));
	}
	public function add_country(){
		$post = $this->input->post();
		$data['action'] = 'add';
		if(!empty($post)){
			$chk = $this->Master_model->checkDuplicate('country','vCountryCode',$post['vCountryCode']);
			if($chk == 0) {
				$add = $this->Master_model->__add_single_data('country',$post);
				$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('master/country') );
			}else{
				$this->load->view('country_action',compact('data'));
			}
		}else{
			$this->load->view('country_action',compact('data'));
		}
	}
	public function edit_country($id=''){
		if(!empty($id)){
			$post = $this->input->post();
			$where = array('iCountryId'=>$id);
			if(empty($post)){
				$data['action'] = 'edit';
				$data['pageData'] = $this->Master_model->__getsingledata('country','*',$where);
				$this->load->view('country_action',compact('data'));
			}else{
				$verificationID = "iCountryId != ".$id;
				$chk = $this->Master_model->checkDuplicate('country','vCountryCode',$post['vCountryCode'],$verificationID);
				if($chk == 0){
					$update = $this->Master_model->__update_single_data('country',$post,$where);
				}
				$this->__setflash( $update, 'Data Updated Successfully .!', 'Error while updating', admin_url('master/country') );
			}
		}else{
			return redirect(admin_url('master/country'));
		}
	}
	/*==========================STATE LIST==========================*/
	public function state(){
		$ssql = " 1 = 1";
		if(!empty(trim($_GET['name']))){
    		$ssql .= " AND vState LIKE '%".trim($_GET['name'])."%' ";
    	}
    	if(!empty(trim($_GET['country']))){
    		$ssql .= " AND iCountryId = '".trim($_GET['country'])."' ";
    	}
        if(!empty(trim($_GET['status']))){
            $ssql .= " AND eStatus = '".trim($_GET['status'])."' ";
        }
		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data['state'] = $this->Master_model->__getwheredata('state','*',$ssql,'vState','ASC',$this->dataLimit,$offset);
		$data['country'] = $this->Master_model->getCountryList($cond,'iCountryId,vCountry');
		$config = $this->configPagination(
        	admin_url('master/state'),
        	$this->Master_model->__getdatacount('state',$ssql),
        	$this->dataLimit,4
        );
		$this->pagination->initialize($config);

		$this->load->view('state',compact('data'));
	}
	public function add_state(){
		$post = $this->input->post();
		$cond = array('eStatus' => 'Active');
		$data['action'] = 'add';
		$data['country'] = $this->Master_model->getCountryList($cond,'iCountryId,vCountry');
		if(!empty($post)){
			$chk = $this->Master_model->checkDuplicate('state','vState',$post['vState']);
			if($chk == 0) {
				$add = $this->Master_model->__add_single_data('state',$post);
				$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('master/state') );
			}else{
				$this->load->view('state_action',compact('data'));
			}
		}else{
			$this->load->view('state_action',compact('data'));
		}
	}
	public function edit_state($id=''){
		if(!empty($id)){
			$post = $this->input->post();
			$where = array('iStateId'=>$id);
			$data['country'] = $this->Master_model->getCountryList($cond,'iCountryId,vCountry');
			if(empty($post)){
				$data['action'] = 'edit';
				$data['pageData'] = $this->Master_model->__getsingledata('state','*',$where);
				$this->load->view('state_action',compact('data'));
			}else{
				$verificationID = "iStateId != ".$id;
				$chk = $this->Master_model->checkDuplicate('state','vState',$post['vState'],$verificationID);
				if($chk == 0){
					$update = $this->Master_model->__update_single_data('state',$post,$where);
				}
				$this->__setflash( $update, 'Data Updated Successfully .!', 'Error while updating', admin_url('master/state') );
			}
		}else{
			return redirect(admin_url('master/state'));
		}
	}
	/*==========================CITY LIST==========================*/
	public function city(){
		$ssql = " 1 = 1";
		if(!empty(trim($_GET['name']))){
    		$ssql .= " AND vCity LIKE '%".trim($_GET['name'])."%' ";
    	}
    	if(!empty(trim($_GET['country']))){
    		$ssql .= " AND iCountryId = '".trim($_GET['country'])."' ";
    	}
        if(!empty(trim($_GET['status']))){
            $ssql .= " AND eStatus = '".trim($_GET['status'])."' ";
        }
		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data['city'] = $this->Master_model->__getwheredata('city','*',$ssql,'iCityId','ASC',$this->dataLimit,$offset);
		$data['country'] = $this->Master_model->getCountryList($cond,'iCountryId,vCountry');
		$config = $this->configPagination(
        	admin_url('master/city'),
        	$this->Master_model->__getdatacount('city',$ssql),
        	$this->dataLimit,4
        );
		$this->pagination->initialize($config);

		$this->load->view('city',compact('data'));
	}
	public function add_city(){
		$post = $this->input->post();
		$data['action'] = 'add';
		$data['country'] = $this->Master_model->getCountryList($cond,'iCountryId,vCountry');
		if(!empty($post)){
			$chk = $this->Master_model->checkDuplicate('city','iCityId',$post['iCityId']);
			if($chk == 0) {
				$add = $this->Master_model->__add_single_data('city',$post);
				$this->__setflash( $add, 'Data Inserted Successfully .!', 'Error', admin_url('master/city') );
			}else{
				$this->load->view('city_action',compact('data'));
			}
		}else{
			$this->load->view('city_action',compact('data'));
		}
	}
	public function edit_city($id=''){
		if(!empty($id)){

			$post = $this->input->post();
			$where = array('iCityId'=>$id);
			$data['country'] = $this->Master_model->getCountryList($cond,'iCountryId,vCountry');
			if(empty($post)){
				$data['action'] = 'edit';
				$data['pageData'] = $this->Master_model->__getsingledata('city','*',$where);
				$this->load->view('city_action',compact('data'));
			}else{
				$verificationID = "iCityId != ".$id;
				$chk = $this->Master_model->checkDuplicate('city','iCityId',$post['iCityId'],$verificationID);
				if($chk == 0){
					$update = $this->Master_model->__update_single_data('city',$post,$where);
				}
				$this->__setflash( $update, 'Data Updated Successfully .!', 'Error while updating', admin_url('master/city') );
			}
		}else{
			return redirect(admin_url('master/city'));
		}
	}

	/*==========================GLOBAL DATA==========================*/
	public function changeStatus(){
		
		$id = $this->input->post('id');
		$type = $this->input->post('actionType');
		$actionTable = $this->input->post('actionTable');
			
		$key = 'iCountryId';
		if($actionTable == 'state'){ $key = 'iStateId';	}
		if($actionTable == 'city'){ $key = 'iCityId';	}
		if($actionTable == 'buses_type'){ $key = 'iBusTypeId';	}
		if($actionTable == 'pages'){ $key = 'iPageId';	}
		if($actionTable == 'product'){ $key = 'iProductId';	}
		if($actionTable == 'subscription_plan'){ $key = 'iPlanId';	}

		$data = array('eStatus' => $type);
		$condition = array($key => $id);
		$status = $this->Master_model->__update_single_data($actionTable, $data, $condition);
		echo $status;
	}
	public function changeBulkStatus(){
		$bulkid = $this->input->post('bulkid');
		$type = $this->input->post('type');
		$actionTable = $this->input->post('table');

		$key = 'iCountryId';
		if($actionTable == "state"){ $key = 'iStateId';	}
		if($actionTable == "city"){ $key = 'iCityId';	}
		if($actionTable == "service_category"){ $key = 'iCategoryId';	}
		if($actionTable == 'pages'){ $key = 'iPageId';	}
		if($actionTable == 'product'){ $key = 'iProductId';	}
		if($actionTable == 'subscription_plan'){ $key = 'iPlanId';	}

		$data = array('eStatus' => $type);
		$status = $this->Master_model->__update_multiple_data($actionTable, $data, $key, $bulkid);
		if($status){
			echo $status;	
		}else{ echo $this->db->last_query(); }	
	}

}
