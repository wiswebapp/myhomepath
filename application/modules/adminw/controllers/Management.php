<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management extends MY_Controller {

	
	public function __construct(){
		parent::__construct();
		is_adminlogged_in();
		$this->load->model('Common_model');
		$this->load->model('Management_model');
	}

	public function index(){
		return redirect(admin_url());
	}

	/*==========================REPORT LIST==========================*/
	public function order_report(){

		$ssql = $sql= "1 = 1";
		if( !empty(trim($_GET['fromdate'])) && !empty(trim($_GET['todate']))){
    		$ssql .= " AND DATE(ord.createdDate) BETWEEN '".toDate(trim($_GET['fromdate']),'Y-m-d')."' AND '".toDate(trim($_GET['todate']),'Y-m-d')."'";
    		//For Get Total Count
    		$sql .= " AND DATE(createdDate) BETWEEN '".toDate(trim($_GET['fromdate']),'Y-m-d')."' AND '".toDate(trim($_GET['todate']),'Y-m-d')."'";
    	}
    	if(!empty(trim($_GET['orderid']))){
    		$ssql .= " AND ord.vOrderId = '".trim($_GET['orderid'])."'";
    		//For Get Total Count
    		$sql .= " AND vOrderId = '".trim($_GET['orderid'])."'";
    	}
    	
		$offset = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data['pagedata'] = $this->Management_model->getOrderReport( $ssql,$offset,$this->dataLimit );
		$data['totalAllData'] = $this->Common_model->__getdatacount('orders',$sql);
		//where while searching
        $config = $this->configPagination(
        	admin_url('management/order-report'),
        	$this->Common_model->__getdatacount('orders',$sql),
        	$this->dataLimit,$offset
        );
		$this->pagination->initialize($config);
		$this->load->view('order_report',compact('data'));
	}
	
	/*==========================GLOBAL FOR CONTROLLER==========================*/
	public function changeStatus(){
		
		$userId = $this->input->post('userid');
		$type = $this->input->post('actionType');
		$actionTable = $this->input->post('actionTable');
			
		$key = 'iBusId';
		if($actionTable == 'register_user'){ $key = 'iUserId'; }
		if($actionTable == 'bus_points'){ $key = 'iPointId'; }
		if($actionTable == 'routes'){ $key = 'iRouteId'; }
		if($actionTable == 'promocode'){ $key = 'iCouponId'; }
		if($actionTable == 'tours'){ $key = 'iTourId'; }
		if($actionTable == 'tours_images'){ $key = 'iImageId'; }
		if($actionTable == 'advertise_banners'){ $key = 'iAdvertBannerId'; }

		$data = array('eStatus' => $type);
		$condition = array($key => $userId);
		$status = $this->Management_model->__update_single_data($actionTable, $data, $condition);
		echo $status;
	}
	public function changeBulkStatus(){
		$userId = $this->input->post('bulkid');
		$type = $this->input->post('type');
		$actionTable = $this->input->post('table');

		$key = 'iBusId';
		if($actionTable == 'register_user'){ $key = 'iUserId'; }
		if($actionTable == 'bus_points'){ $key = 'iPointId'; }
		if($actionTable == 'routes'){ $key = 'iRouteId'; }
		if($actionTable == 'promocode'){ $key = 'iCouponId'; }
		if($actionTable == 'tours'){ $key = 'iTourId'; }
		if($actionTable == 'tours_images'){ $key = 'iImageId'; }
		if($actionTable == 'advertise_banners'){ $key = 'iAdvertBannerId'; }

		$data = array('eStatus' => $type);
		$status = $this->Management_model->__update_multiple_data($actionTable, $data, $key, $userId);
		echo $status;
	}
	public function send_push_notification(){

		$this->load->model('Users_model');
		$post = $this->input->post();

		$ssql = "eStatus = 'Active'";
		$data['usersList'] = $this->Users_model->getRegisterUsers('iUserId,CONCAT(vName," ",vLastName) AS vName,vFirebaseDeviceToken', NULL, NULL, '');
		$data['agentList'] = $this->Users_model->getAgentUser('iAgentId,CONCAT(vFirstName," ",vLastName) AS vName,vFirebaseDeviceToken', NULL, NULL, $ssql);

		if(empty($post)){
			$this->load->view('send_pushnotification',compact('data'));
		}else{
			
			$iUser = isset($_POST['iUser']) ? $_POST['iUser'] : '';
		    $iUserId = isset($_POST['iUserId']) ? $_POST['iUserId'] : '';
		    $iAgentId = isset($_POST['iAgentId']) ? $_POST['iAgentId'] : '';
		    $MSGTITLE = isset($_POST['MSGTITLE']) ? $_POST['MSGTITLE'] : '';
		    $MSGBODY = isset($_POST['MSGBODY']) ? $_POST['MSGBODY'] : '';

		    if($iUser == "Users"){
		    	$ssql = "iUserId = $iUserId";
				$userData = $this->Users_model->getRegisterUsers('vFirebaseDeviceToken', NULL, NULL, $ssql);
		    }elseif($iUser == "Agent") {
		    	$ssql = "iAgentId = $iAgentId";
				$userData = $this->Users_model->getAgentUser('vFirebaseDeviceToken', NULL, NULL, $ssql);
		    }elseif($iUser == "AllUsers"){
		    	$userData = $this->Users_model->getRegisterUsers('vFirebaseDeviceToken', NULL, NULL, '');
		    }elseif($iUser == "AllAgent"){
		    	$userData = $this->Users_model->getAgentUser('vFirebaseDeviceToken', NULL, NULL, '');
		    }
			
			$returnmessage = TRUE;
			foreach ($userData['data'] as $value) {
				$registatoin_ids = $value['vFirebaseDeviceToken'];
				
				if( strlen($registatoin_ids) > 100){
					$send = $this->sendPushFromWebtoDroid($registatoin_ids, $MSGTITLE, $MSGBODY);
					if(!$send){
						$returnmessage = FALSE;
					}
				}
			}
			
			$this->__setflash( TRUE, 'Push notification sent Successfully to user(s).', 'Some Error preserved while sending a notification. please try again.', admin_url('management/send-push-notification'));
		}
	}
}

?>