<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('default_socket_timeout', 10);
ini_set('memory_limit', '-1');

include_once('Webservice_organization.php');

class Webservice extends Webservice_organization {

	public function __construct(){
        parent::__construct();
        $this->load->model('Webservice_model');
        $this->load->model('Validate_model');
        $this->load->model('Profile_model');
        $this->load->model('Search_model');
        $this->load->model('Booking_model');
        $this->load->model('Common_front_model');

        $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
        
        if(empty($type)){
	        $result['Action'] = "0";
	        $result['message'] = 'Required parameter missing.';
	        echo json_encode($result);
	        exit;
    	}
    	
		$typeArray = $this->allTypeList();

        if ( in_array($type, $typeArray)) {

        	$vSessionId = isset($_REQUEST['vSessionId']) ? trim($_REQUEST['vSessionId']) : '';
        	$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
        	$GeneralUserType = isset($_REQUEST['GeneralUserType']) ?
        	trim($_REQUEST['GeneralUserType']) : '';

        	$primarykey = "iOrgId";
        	if($GeneralUserType == "User"){
        		$primarykey = "iUserId";
        	}

        	if ($vSessionId == "" || $GeneralMemberId == "" || $GeneralUserType == "") {
        		$this->sessionOut();
        	} else {
        		$userData = $this->Profile_model->getProfileDetail($GeneralUserType,$GeneralMemberId);
        	}

        	if ($userData[0][$primarykey] != $GeneralMemberId || $userData[0]['vSessionId'] != $vSessionId) {
        		$this->sessionOut('UNAUTHORIZED ACCESS');
        	}
        }
    }

    public function test007(){
    	$emailData['vOrgName'] = "Barbecuw Nation Salon";
		$emailData['vUserEmail'] = "balbvandtray123@gmail.com";
		$emailData['iOrgId'] = "SALON25656";
    	$data = $this->getEmailTemplate('TEMPLATE_ORGANIZATION_REGISTER',$emailData);
    	printr($data);

    }
    public function index(){
    	$this->sessionOut();
    }

    public function signin(){
    	
    	$username = isset($_REQUEST['uname']) ? cleanString($_REQUEST['uname']) : '';
	    $password = isset($_REQUEST['pass']) ? cleanString($_REQUEST['pass']) : '';
	    $vFirebaseDeviceToken = isset($_REQUEST['vFirebaseDeviceToken']) ? cleanString($_REQUEST['vFirebaseDeviceToken']) : '';

	    if(!empty($username) && !empty($password)){
			
			$authenticate = $this->Validate_model->isUserValid( $username,$password );
			
			if( $authenticate['Action'] == TRUE){
				if($authenticate['Msg'] == "LOGIN_SUCCS"){
		    		
		    		//update tsession id
		    		$vSessionId = session_id().time();
	    			$userId = $authenticate[0]['iUserId'];
	    			$fullName = $authenticate[0]['vName'];
		    		$vEmail = $authenticate[0]['vEmail'];
		    		$vPhone = $authenticate[0]['vPhone'];

		    		$dataUpdate['vSessionId'] = $vSessionId;
		    		$dataUpdate['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
		    		$dataUpdate['eLogout'] = 'No';
		    		$condition['vEmail'] = $username;
		    		$condition['iUserId'] = $userId;
		    		$update = $this->Validate_model->__update_single_data('register_user',$dataUpdate,$condition);
		    			
		    		$returnArr['Action'] = "1";
		    		$returnArr['message'] = array(
		    									'vSessionId' => $vSessionId,
		    									'GeneralMemberId' => $userId,
		    									'GeneralUserType' => 'User',
		    									'GeneralUserName' => $fullName,
		    									'GeneralUserEmail' => $vEmail,
		    									'GeneralUserPhone' => $vPhone,
		    								);
		    			
		    	}else{
		    		$returnArr['Action'] = "2";
		    		$returnArr['message'] = "Seems like user is blocked or inactive";
		    	}
		    }else{
		    	$returnArr['Action'] = "0";
		    	$returnArr['message'] = "Invalid Username/Password";
		    }
	    }else{
		    $this->sessionOut('MISSING_PARAM');
		}
	    echo json_encode($returnArr);
    }
    public function updateMobileNumber(){
    	$vPhone = isset($_REQUEST['vPhone']) ? cleanString($_REQUEST['vPhone']) : '';
    	$vEmail =  isset($_REQUEST['vEmail']) ? cleanString($_REQUEST['vEmail']) : '';
    	$vRefCode =  isset($_REQUEST['vRefCode']) ? cleanString($_REQUEST['vRefCode']) : '';
    	$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? cleanString($_REQUEST['GeneralUserType']) : '';

    	$userTableData = $this->getUserTableData($GeneralUserType);

    	if(!empty($vRefCode)){
			$whereRef = array('vRefCode'=>$vRefCode);
			$refUserData = $this->Validate_model->__getsingledata('register_user','iUserId',$whereRef);
			if(!empty($refUserData)){
				$data['iRefUserId'] = $refUserData[0]['iUserId'];
				$data['dRefDate'] = date('Y-m-d');
			}else{
				$this->sessionOut('Entered Referral Code is Invalid');
			}
		}

    	$data['vPhone'] = $vPhone;
    	$condition['vEmail'] = $vEmail;
    	$update = $this->Validate_model->__update_single_data($userTableData['tablename'],$data,$condition);
    	if($update){

    		$whereRef = array('vEmail'=>$vEmail);
    		$UserData = $this->Validate_model->__getsingledata('register_user','*',$whereRef);
    		$returnArr['Action'] = "1";
    		$returnArr['message'] = array(
		    						'vSessionId' => $UserData[0]['vSessionId'],
		    						'GeneralMemberId' => $UserData[0]['iUserId'],
		    						'GeneralUserType' => $GeneralUserType,
		    						'GeneralUserName' => $UserData[0]['vName'],
		    						'GeneralUserEmail' => $UserData[0]['vEmail'],
		    						'GeneralUserPhone' => $UserData[0]['vPhone'],
		    						'vFirebaseDeviceToken' => $UserData[0]['vFirebaseDeviceToken']
		    					);
    	}else{
    		$this->sessionOut('Failed In Updating');
    	}
    	echo json_encode($returnArr);
    }
    public function signinwithsocial(){

    	$vName = isset($_REQUEST['vName']) ? cleanString($_REQUEST['vName']) : '';
    	$username = isset($_REQUEST['uname']) ? cleanString($_REQUEST['uname']) : '';
	    /*$password = isset($_REQUEST['pass']) ? cleanString($_REQUEST['pass']) : '';*/
	    $usertype = isset($_REQUEST['usertype']) ? cleanString($_REQUEST['usertype']) : '';
	    $vFirebaseDeviceToken = isset($_REQUEST['vFirebaseDeviceToken']) ? cleanString($_REQUEST['vFirebaseDeviceToken']) : '';
	    
	    if(!empty($usertype) && !empty($username)){

	    	if(ucFirst(strtolower($usertype)) == "User"){
		    	$authenticate = $this->Validate_model->isEmailExist( 'register_user',$username, TRUE);
		    	$vSessionId = session_id().time();

		    	if( empty($authenticate) ){
		    		//do Register
		    		$fullName = $vName;
		    		$vEmail = $username;
		    		$post['vName'] = $vName;
		    		$post['vEmail'] = $username;
		    		$post['tRegistrationDate'] = date('Y-m-d');
					$post['vPhoneCode'] = '91';
					$post['eStatus'] = 'Active';
					$post['eSignUpType'] = 'Google';
					$post['eEmailVerified'] = 'Yes';
					$post['vSessionId'] = $vSessionId;
					$post['vRefCode'] = $this->generateReferralCode('User');
					$adduser = $this->Validate_model->__add_single_data('register_user',$post);
					$userId = $adduser;
					//For Update Firebase Token 
		    		if(!empty($vFirebaseDeviceToken)){
		    			$dataUpdate['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
		    		}
		    		$dataUpdate['eLogout'] = 'No';
		    		$condition = array('eStatus' => "Active","vEmail"=>$username,'iUserId'=>$userId);
		    		$update = $this->Validate_model->__update_single_data('register_user',$dataUpdate,$condition);
		    	}else{
		    		//do Login
		    		$fullName = $authenticate[0]['vName']." ".$authenticate[0]['vLastName'];
		    		$vEmail = $authenticate[0]['vEmail'];
		    		$vPhone = $authenticate[0]['vPhone'];
		    		$userId = $authenticate[0]['iUserId'];
		    		//For Update Firebase Token 
		    		if(!empty($vFirebaseDeviceToken)){
		    			$dataUpdate['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
		    		}
		    		$dataUpdate['eLogout'] = 'No';
		    		$dataUpdate['vSessionId'] = $vSessionId;
		    		$condition = array('eStatus' => "Active","vEmail"=>$username);
		    		$update = $this->Validate_model->__update_single_data('register_user',$dataUpdate,$condition);
		    	}

		    	$returnArr['Action'] = "1";
		    	$returnArr['message'] = array(
		    								'vSessionId' => $vSessionId,
		    								'GeneralMemberId' => $userId,
		    								'GeneralUserType' => $usertype,
		    								'GeneralUserName' => $fullName,
		    								'GeneralUserEmail' => $vEmail,
		    								'GeneralUserPhone' => $vPhone,
		    								'vFirebaseDeviceToken' => $vFirebaseDeviceToken
		    							);
		    }

	    }else{
		    $this->sessionOut();
		}
	    echo json_encode($returnArr);
    }
    /*---------------------SIGNUP USER / PROVIDER / STORE---------------------*/
    public function signup(){

    	$vName = isset($_REQUEST['vName']) ? cleanString($_REQUEST['vName']) : '';
    	$vPhone = isset($_REQUEST['vPhone']) ? cleanString($_REQUEST['vPhone']) : '';
    	$vEmail = isset($_REQUEST['vEmail']) ? cleanString($_REQUEST['vEmail']) : '';
    	$vPassword = isset($_REQUEST['vPassword']) ? cleanString($_REQUEST['vPassword']) : '';
    	$vFirebaseDeviceToken = isset($_REQUEST['vFirebaseDeviceToken']) ? cleanString($_REQUEST['vFirebaseDeviceToken']) : '';
    	$eSignUpType = isset($_REQUEST['eSignUpType']) ? cleanString($_REQUEST['eSignUpType']) : 'Normal';
    	$eEmailVerified = isset($_REQUEST['eEmailVerified']) ? cleanString($_REQUEST['eEmailVerified']) : 'No';
    	$vAddress = isset($_REQUEST['vAddress']) ? $_REQUEST['vAddress'] : '';
    	$vLat = isset($_REQUEST['vLat']) ? $_REQUEST['vLat'] : '';
    	$vLong = isset($_REQUEST['vLong']) ? $_REQUEST['vLong'] : '';

    	$post = $_REQUEST;
    	$post['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
    	$post['eSignUpType'] = $eSignUpType;
    	$post['eEmailVerified'] = $eEmailVerified;
    	$post['vAddress'] = $vAddress;
		$post['vLat'] = $vLat;
		$post['vLong'] = $vLong;
    	unset($post['type']);

    	if( !empty($vName)  && !empty($vEmail) ){
    		//removed other fields for google login purpose
    		$isEmailExist = $this->Validate_model->isEmailExist('register_user','vEmail',$vEmail,TRUE);
    		if( empty($isEmailExist) ){
    			if(!empty($vPassword)){
					$post['vPassword'] = encryptPassword($post['vPassword']);
				}
    			$post['tRegistrationDate'] = date('Y-m-d');
				$post['eStatus'] = "Active";
				$adduser = $this->Validate_model->__add_single_data('register_user',$post);
				//echo $this->db->last_query();exit;
				/*//===================================Sending Email
				$emailData['userName'] = $post['vName']." ".$post['vLastName'];
				$emailData['userEmail'] = $post['vEmail'];
				$emailMessage = $this->getEmailTemplate('TMPLT_USER_REGISTER',$emailData);
				$NOREPLY_EMAIL = $this->__getConfiguration('NOREPLY_EMAIL');
				$this->sendEmailFromSystem($NOREPLY_EMAIL,APP_TITLE, $post['vEmail'], $emailMessage['subject'], $emailMessage['message']);*/
				if($adduser){
					$returnArr['Action'] = "1";
	   				$returnArr['message'] = "Registred Successfully";
	   				$returnArr['userdata'] = array('iUserId'=>$adduser,'vEmail'=>$post['vEmail']);
				}
    		}else{
    			$returnArr['Action'] = "2";
	   			$returnArr['message'] = "User with this email is already Exist.";
    		}
    	}else{
    		$returnArr['Action'] = "0";
	   		$returnArr['message'] = "All Fields are required";
    	}
    	echo json_encode($returnArr);
    	exit;
	}
    
	public function getStoreId($vUserMobile){

		$storeId = generateStoreId($vUserMobile);
		$isIdExist = $this->Validate_model->isEmailExist('organization','vOrgId',$storeId,TRUE);
		if(empty($isIdExist)){
			return $storeId;
		}else{
			getStoreId($vUserMobile);
		}
	}
	public function forgetPassword(){
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
		$vEmail = isset($_REQUEST['vEmail']) ? $_REQUEST['vEmail'] : '';

		$getUserTableData = $this->getUserTableData($GeneralUserType);
		$tablename = $getUserTableData['tablename'];
		$primarykey = $getUserTableData['primarykey'];
		$usertype = $getUserTableData['usertype'];


		if($primarykey == "iUserId"){
			//for user
			$sendEmail = $this->Common_front_model->resetPasswordModel($vEmail,$usertype);
			if($sendEmail == TRUE){

				$ADMIN_EMAIL = $this->__getConfiguration('NOREPLY_EMAIL');
				$emailMessage = $this->getEmailTemplate('TMPLT_FORGET_PASSWORD');

				$sendEmail = $this->sendEmailFromSystem($ADMIN_EMAIL, APP_TITLE, $vEmail, $emailMessage['subject'], $emailMessage['message']);

				if($sendEmail){
					$returnArr['Action'] = "1";
					$returnArr['message'] = "Password Reset Mail has been sent Successfully.";	
				}else{
					$this->sessionOut('Error in Sending mail.Please try again.!');
				}
			}else{
				$this->sessionOut('Email Is not Valid');
			}
		}else{
			//for agent forget password
		}
		echo json_encode($returnArr);
	}
	public function signout(){

		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
		$vSessionId = isset($_REQUEST['vSessionId']) ? $_REQUEST['vSessionId'] : '';
		$vSessionId = isset($_REQUEST['vSessionId']) ? $_REQUEST['vSessionId'] : '';

		$update['eLogout'] = "Yes";
		$update['vSessionId'] = "";
		$logout = $this->Webservice_model->logoutUser($GeneralMemberId,$GeneralUserType,$update);
		/*echo $this->db->last_query();exit;*/
		if($logout){
			$returnArr['Action'] = 1;
			$returnArr['message'] = "Logout Success";
		}else{
			$returnArr['Action'] = 0;
			$returnArr['message'] = "Logout Failed";
		}
		echo json_encode($returnArr);
	}
	public function generalData(){
		$GeneralUserType = isset($_REQUEST["GeneralUserType"]) ? trim($_REQUEST["GeneralUserType"]) : '';
	    $GeneralMemberId = isset($_REQUEST["GeneralMemberId"]) ? trim($_REQUEST["GeneralMemberId"]) : '';

	    //For getting all basic general data
		$getGeneralData = $this->Webservice_model->getGeneralData();
		//For getting user information
		$getGeneralData['profiledata'] = $this->Profile_model->getProfileDetail($GeneralUserType,$GeneralMemberId);

		//For getting Wallet balance
		$walletData = $this->Profile_model->getWalletData( $GeneralUserType,$GeneralMemberId);
		$walletBalance = 0;
		foreach ($walletData as $value) {
			if($value['eType'] == "Credit"){
				$walletBalance = $walletBalance + $value['iBalance'];
			}else{
				$walletBalance = $walletBalance - $value['iBalance'];
			}
		}
		$getGeneralData['walletBalance'] = $walletBalance;

		$returnArr['Action'] = "1";
		$returnArr['message'] = $getGeneralData;
		echo json_encode($returnArr);
	}
	public function generalConfigData(){
		
		//Configuration Variables
		$generalConfigData = $this->__getConfiguration();
		foreach ($generalConfigData as $configValue) {
			$config[$configValue['vName']] = $configValue['vValue'];
		}
		$returnArr = $config;

		$languageLabels = $this->Profile_model->getLanguageLabel();
		$returnArr['languageLabel'] = $languageLabels;
		//printr($returnArr);
		echo json_encode($returnArr);
		echo json_encode($returnArr);
	}

	public function updateuserAddress(){
		$GeneralUserType = isset($_REQUEST["GeneralUserType"]) ? cleanString($_REQUEST["GeneralUserType"]) : '';
	    $GeneralMemberId = isset($_REQUEST["GeneralMemberId"]) ? cleanString($_REQUEST["GeneralMemberId"]) : '';
	    $vAddress = isset($_REQUEST['vAddress']) ? $_REQUEST['vAddress'] : '';
		$vLat = isset($_REQUEST['vLat']) ? $_REQUEST['vLat'] : '';
		$vLong = isset($_REQUEST['vLong']) ? $_REQUEST['vLong'] : '';

		$Data_update_User['vAddress'] = $vAddress;
		$Data_update_User['vLat'] = $vLat;
		$Data_update_User['vLong'] = $vLong;

		$whre = array('iUserId' => $GeneralMemberId);
	    $update = $this->Webservice_model->__update_single_data('register_user',$Data_update_User,$whre);

	    $returnArr['Action'] = 1;
	    $returnArr['message'] = 'Updated Successfully .!';
	    echo json_encode($returnArr);
	    exit;

	}
	public function updateuserprofile(){
	    
	    $GeneralUserType = isset($_REQUEST["GeneralUserType"]) ? cleanString($_REQUEST["GeneralUserType"]) : '';
	    $GeneralMemberId = isset($_REQUEST["GeneralMemberId"]) ? cleanString($_REQUEST["GeneralMemberId"]) : '';
		$vName = isset($_REQUEST['vName']) ? $_REQUEST['vName'] : '';
		$vPhone = isset($_REQUEST['vPhone']) ? $_REQUEST['vPhone'] : '';
		$vEmail = isset($_REQUEST['vEmail']) ? $_REQUEST['vEmail'] : '';
		$vAddress = isset($_REQUEST['vAddress']) ? $_REQUEST['vAddress'] : '';
		$vLat = isset($_REQUEST['vLat']) ? $_REQUEST['vLat'] : '';
		$vLong = isset($_REQUEST['vLong']) ? $_REQUEST['vLong'] : '';

	    $userData = $this->Profile_model->getProfileDetail($GeneralUserType,$GeneralMemberId);

	    if($GeneralUserType == "User"){
	    	$Data_update_User['vName'] = $vName;
	    	$Data_update_User['vPhone'] = $vPhone;
	    	$Data_update_User['vEmail'] = $vEmail;
			$Data_update_User['vAddress'] = $vAddress;
			$Data_update_User['vLat'] = $vLat;
			$Data_update_User['vLong'] = $vLong;
	    }

	    $whre = array('iUserId' => $GeneralMemberId);
	    $update = $this->Webservice_model->__update_single_data('register_user',$Data_update_User,$whre);


	    if($GeneralUserType == "User"){
		    //changing date format for final view
		    if($dBirthDate == "1970-01-01" || $dBirthDate == "0000-00-00" || empty($dBirthDate)){
		    	$dBirthDate = "";
		    }else{
		    	$dBirthDate = toDate($dBirthDate,'d-m-Y');
		    }
		    $Data_update_User['dBirthDate'] = $dBirthDate;
		}
	    
	    
	    $returnArr['Action'] = 1;
	    $returnArr['message'] = array();
	    $returnArr['message'] = $Data_update_User;
	    $returnArr['message']['vEmail'] = $userData[0]['vEmail'];
	    $returnArr['update'] = $update;
	    echo json_encode($returnArr);
	}

	public function getStaticPage(){

		$pageId = isset($_REQUEST['pageId']) ? $_REQUEST['pageId'] : '';

		if(!empty($pageId)){

			$data = $this->Common_front_model->getCMSPagesData($pageId,'Yes');

			$returnArr['Action'] = "1";
			$returnArr['message'] = $data;
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "INVALID_REQUEST";
		}
		echo json_encode($returnArr);
		exit;
	}
	public function changepassword(){
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
        $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
		$currentpass = isset($_REQUEST['currentpass']) ? $_REQUEST['currentpass'] : '';
		$newpass = isset($_REQUEST['newpass']) ? $_REQUEST['newpass'] : '';
		$vEmail = isset($_REQUEST['vEmail']) ? $_REQUEST['vEmail'] : '';

		$tableData = $this->getUserTableData($GeneralUserType);
		$tablename = $tableData['tablename'];
		$primarykey = $tableData['primarykey'];
		$usertype = $tableData['usertype'];

		$resultQ = $this->Validate_model->isEmailExist($tablename,$vEmail,TRUE);

		if( verifyPassword($currentpass,$resultQ[0]['vPassword']) ){
			$condition = array($primarykey=>$GeneralMemberId);
			if(!empty($newpass)){
				$updatedata['vPassword'] = encryptPassword($newpass);
			}
			$update = $this->Validate_model->__update_single_data($tablename,$updatedata,$condition);
			$returnArr['Action'] = "1";
			$returnArr['message'] = "Password has been updated Successfully.!";
		}else{
			$this->sessionOut('Current Password is invalid.!');
		}
		echo json_encode($returnArr);
    }
    public function getProductList(){

		$categoryId = isset($_REQUEST['categoryId']) ? $_REQUEST['categoryId'] : '';
		$tconfig = $this->tconfig();

		if(!empty($categoryId)){
			$data = $this->Common_front_model->getProductModel($categoryId);

			for ($i=0; $i < count($data); $i++) {
				$logo = $data[$i]['vProductImage'];
				$data[$i]['vProductImage'] = $tconfig['product_path'].$logo;
			}

			$returnArr['Action'] = "1";
			$returnArr['message'] = $data;
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "No Data Found";
		}
		echo json_encode($returnArr);
	}

	public function subscribePlan(){

		$productId = isset($_REQUEST['productId']) ? $_REQUEST['productId'] : '';
		$tconfig = $this->tconfig();

		if(!empty($productId)){
			$data = $this->Common_front_model->getProductData('iProductId = '.$productId);

			for ($i=0; $i < count($data); $i++) {
				$logo = $data[$i]['vProductImage'];
				$data[$i]['vProductImage'] = $tconfig['product_path'].$logo;
			}

			$returnArr['Action'] = "1";
			$returnArr['message'] = $data[0];
			$ssqlPlan = "sp.iProductId = ".$data[0]['iProductId'];
			$returnArr['message']['plan'] = $this->Common_front_model->getSubscriptionPlanMyModl($ssqlPlan);
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "No Data Found";
		}
		echo json_encode($returnArr);
	}

	public function getOrders(){

		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
		$paymentType = isset($_REQUEST['paymentType']) ? $_REQUEST['paymentType'] : 'Normal';
		//printr($_REQUEST);
		if(!empty($GeneralMemberId)){
			$data = $this->Common_front_model->getOrderReport($GeneralMemberId,$GeneralUserType,$paymentType);
			
			$returnArr['Action'] = "1";
			$returnArr['message'] = $data;
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "No Data Found";
		}
		echo json_encode($returnArr);
		exit;
	}
	
	public function getSingleOrderDetail(){

		$orderId = isset($_REQUEST['orderId']) ? $_REQUEST['orderId'] : '';
		$orderData = $this->Common_front_model->getSingleOrderDetail($orderId);
		if(!empty($orderData)){
			$returnArr['Action'] = "1";
			$returnArr['message'] = $orderData[0];
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "No Data Found";
		}
		echo json_encode($returnArr);
		exit;
	}

	public function acceptOrder(){

		$orderId = isset($_REQUEST['orderId']) ? $_REQUEST['orderId'] : '';
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';

		$orderData = $this->Common_front_model->getSingleOrderDetail($orderId);

		if(!empty($orderData) && empty($orderData[0]['ProviderName'])){

			$orderStts['iProviderId'] = $GeneralMemberId;
			$orderStts['eStatus'] = 'Accepted';
			$condition['vOrderId'] = $orderId;
		    $update = $this->Validate_model->__update_single_data('orders',$orderStts,$condition);

		    if($update){
		    	$orderData = $this->Common_front_model->getSingleOrderDetail($orderId);

				$returnArr['Action'] = "1";
				$returnArr['message'] = $orderData[0];
			}
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "Order is Already Assigned";
		}
		echo json_encode($returnArr);
		exit;
	}

	public function completeOrder(){

		$orderId = isset($_REQUEST['orderId']) ? $_REQUEST['orderId'] : '';
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';

		$orderData = $this->Common_front_model->getSingleOrderDetail($orderId);
		
		if(!empty($orderData) 
			&& !empty($orderData[0]['ProviderName'] 
				&& $orderData[0]['eStatus'] == "Accepted")){
			$orderStts['eStatus'] = 'Completed';
			$condition['vOrderId'] = $orderId;
		    $update = $this->Validate_model->__update_single_data('orders',$orderStts,$condition);

		    $orderData = $this->Common_front_model->getSingleOrderDetail($orderId);
			$returnArr['Action'] = "1";
			$returnArr['message'] = $orderData[0];
			
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "Order is not accepted Yet.";
		}
		echo json_encode($returnArr);
		exit;
	}

	public function makePayment(){

		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
		$iProductId = isset($_REQUEST['iProductId']) ? $_REQUEST['iProductId'] : '';
		$iCategoryId = isset($_REQUEST['iCategoryId']) ? $_REQUEST['iCategoryId'] : '';
		$iSubscriptionPlan = isset($_REQUEST['iSubscriptionPlan']) ? $_REQUEST['iSubscriptionPlan'] : '';
		$vProductName = isset($_REQUEST['vProductName']) ? $_REQUEST['vProductName'] : '';
		$paymentType = isset($_REQUEST['paymentType']) ? $_REQUEST['paymentType'] : '';
		$iAmount = isset($_REQUEST['iAmount']) ? $_REQUEST['iAmount'] : '';
		$planStartDate = isset($_REQUEST['planStartDate']) ? $_REQUEST['planStartDate'] : '';
		$iQty = isset($_REQUEST['iQty']) ? $_REQUEST['iQty'] : '';
		$eFrequency = isset($_REQUEST['eFrequency']) ? $_REQUEST['eFrequency'] : '';
		$vAddress = isset($_REQUEST['vAddress']) ? $_REQUEST['vAddress'] : '';
		$vAddressLat = isset($_REQUEST['vAddressLat']) ? $_REQUEST['vAddressLat'] : '';
		$vAddressLong = isset($_REQUEST['vAddressLong']) ? $_REQUEST['vAddressLong'] : '';
		$orderId = "O".time();
		$returnArr = array();
	  	//Order Data
	    $orderData['iProductId'] = $iProductId;
	    $orderData['iProductCatId'] = $iCategoryId;
	    $orderData['paymentType'] = $paymentType;
	    $orderData['iFare'] = $iAmount;
	    $orderData['planStartDate'] = toDate($planStartDate,'Y-m-d');
	    $orderData['iQty'] = $iQty;
	    $orderData['eFrequency'] = $eFrequency;
	    $orderData['iSubscriptionPlan'] = $iSubscriptionPlan;
	    $orderData['iUserId'] = $GeneralMemberId;
	    $orderData['vOrderId'] = $orderId;
	    $orderData['vAddress'] = $vAddress;
	    $orderData['vAddressLat'] = $vAddressLat;
	    $orderData['vAddressLong'] = $vAddressLong;
	    $orderData['createdDate'] = date('Y-m-d');
	    $orderData['eStatus'] = 'Pending';

	    $inertOrder = $this->Common_front_model->__add_single_data('orders',$orderData);
	    //printr($this->db->last_query());
	    if($inertOrder){
	    	$returnArr['Action'] = 1;
	    	$returnArr['message']['orderId'] = $orderId;
	    	//$returnArr['message']['paymentUrl'] = base_url('payment/paymentRequest/'.$orderId);
	    }else{
	    	$returnArr['Action'] = 0;
	    	$returnArr['message']['orderId'] = "Something Went Wrong";
	    }
	    echo json_encode($returnArr);
	    exit;
	}

	public function confirmPayment(){
		$iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : '';
		$txnid = isset($_REQUEST['txnid']) ? $_REQUEST['txnid'] : '';
		$udf5 = isset($_REQUEST['udf5']) ? $_REQUEST['udf5'] : '';
		$orderId = isset($_REQUEST['orderId']) ? $_REQUEST['orderId'] : '';
		$amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : '';
		$postdata = isset($_REQUEST['postdata']) ? $_REQUEST['postdata'] : '';
		$paymentStatus = isset($_REQUEST['paymentStatus']) ? $_REQUEST['paymentStatus'] : 'Fail';//Pass
		$returnArr['Action'] = 0;
		$returnArr['message'] = "Something Went Wrong in Payment";
		
		if(!empty($iUserId) && !empty($txnid) && !empty($orderId) && !empty($amount) && !empty($postdata))
		{
			//ADd Data to payment
			$paymentData['vPaymentStatus'] = "UnPaid";
			$isPaid = 0;
			if($paymentStatus == "Pass"){
				$isPaid = 1;
				$paymentData['vPaymentStatus'] = "Paid";
				//Update Order Status
				$updateOrderArr['eStatus'] = "Placed";
				$cond['vOrderId'] = $orderId;
				$updtOrdr = $this->Booking_model->__update_single_data('orders',$updateOrderArr,$cond);
			}
			$paymentData['iUserId'] = $iUserId;
			$paymentData['vTXNID'] = $txnid;
			$paymentData['tPaymentMsg'] = $postdata['udf5'];
			$paymentData['iOrderId'] = $orderId;
			$paymentData['iAmount'] = $amount;
			$paymentData['tPaymentDetails'] = json_encode($postdata);
			$paymentData['vPaymentMode'] = "ONLINE";
			$paymentData['dPaymentDate'] = date('Y-m-d h:i:s');
			$updtOrdr = $this->Booking_model->__add_single_data('payments',$paymentData);
			
			$returnArr['Action'] = $isPaid;
			$returnArr['message'] = $paymentData;
		}

		echo json_encode($returnArr);
		exit;
	}

	public function cronDetail(){
    	
    	$vSessionId = isset($_REQUEST['vSessionId']) ? $_REQUEST['vSessionId'] : '';
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';

		$returnArr['Action'] = 1;
		$tconfig = $this->tconfig();
		//Basic User Details
		$userData = $this->Profile_model->getProfileDetail($GeneralUserType,$GeneralMemberId);
		$returnArr['message'] = $userData[0];
		if($GeneralUserType == "User"){
			//For Adding Service Category List
			$ssqlCat = array('eStatus'=>'Active');
			$categoryList = $this->Common_front_model->getServiceCategory($ssqlCat);
			for ($i=0; $i < count($categoryList); $i++) {
				$logo = $categoryList[$i]['vLogo'];
				$categoryList[$i]['vLogo'] = $tconfig['category_path'].$logo;
			}
			$returnArr['message']['categoryList'] = $categoryList;
		}else{
			//Get Last Job Start & End Time
			$getLogReport = $this->Common_front_model->letLastLogReport($GeneralMemberId);
			$returnArr['message']['attendance'] = $getLogReport;
			//For Organization / Provider Get Orders If Placed by User
			$getPendingOrder = $this->Common_front_model->getPendingOrders($userData[0]['vLocationLat'],$userData[0]['vLocationLong']);
			$returnArr['message']['pendingOrderList'] = $getPendingOrder;

		}
		
	    echo json_encode($returnArr);
	    exit;
    }
}