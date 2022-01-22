<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General_Controller extends GeneralEmail_Controller {
	
	public $dataLimit;
	public $loginData;
	public $defaultLanguage;

	//for admin panel
	public $generalData;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(ADMIN_FOLDER."/Setting_model");

		$this->dataLimit = $this->__getConfiguration('PAGE_DATA_LIMIT');

		//For getting Login User Data
		$this->load->model('Dashboard_model');
		if( is_userlogged_in() ){
        	$hash = $this->session->userdata('iUserId');
        	$userType = $this->session->userdata('UserType');
        	$this->loginData = $this->Dashboard_model->getLoginData($hash,$userType);
        }elseif( is_agentlogged_in() ){
            $hash = $this->session->userdata('iAgentId');
            $userType = $this->session->userdata('UserType');
            $this->loginData = $this->Dashboard_model->getLoginData($hash,$userType);
        }

        //General Application Data
        $generalData = $this->Setting_model->getGeneralData();
        foreach ($generalData as $generalValue) {
        	$customGData[$generalValue['vName']] = $generalValue['vValue'];
        }
        $this->generalData = $customGData;
	}

	protected function getWalletTransId(){
		$walletTransId = "TRANS".date('ymd').time().rand(1111,9999);
		return $walletTransId;
	}

	protected function getBookingID(){
		$bookingId = "BOOKING".time();
		return $bookingId;
	}

	protected function generateReferralCode($userType = ''){
		$loop = 6;
		if($userType == "User"){ $res = "US"; $loop = 8; }
		if($userType == "Agent"){ $res = "AG"; $loop = 8; }

		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		for ($i = 0; $i < $loop; $i++) {
		    $res .= $chars[mt_rand(0, strlen($chars)-1)];
		}
		return $res;
	}

	public function getUserTableData($userType){
    	$userType = strtolower($userType);

    	if($userType == "user"){
    		$returnArr['tablename'] = 'register_user';
    		$returnArr['primarykey'] = 'iUserId';
            $returnArr['usertype'] = 'User';
    	}elseif($userType == "organization"){
    		$returnArr['tablename'] = 'organization';
    		$returnArr['primarykey'] = 'iOrgId';
            $returnArr['usertype'] = 'Organization';
    	}else{
    		$returnArr['tablename'] = 'register_agent';
    		$returnArr['primarykey'] = 'iAgentId';
            $returnArr['usertype'] = 'Agent';
    	}
    	return $returnArr;
    }
    
	public function getStateData(){
		$countryId = $this->input->post('countryId');
		$selectedId = $this->input->post('selectedId');
		$op = "";
		$stateData = $this->Setting_model->__getsingledata('state','*',array('iCountryId'=>$countryId));
		if(!empty($stateData)){
			foreach ($stateData as $value) {
				if($value['iStateId'] == $selectedId){
					$op .= "<option selected value='".$value['iStateId']."'>".$value['vState']."</option>";
				}else{
					$op .= "<option value='".$value['iStateId']."'>".$value['vState']."</option>";
				}
			}
		}else{
			$op .= "<option value=''>Please Select Country first</option>";
		}
		echo $op;exit;
	}
	
	public function getCityData(){
		$stateId = $this->input->post('stateId');
		$selectedId = $this->input->post('selectedId');
		$op = "";
		
		$cityData = $this->Setting_model->__getsingledata('city','*',array('iStateId'=>$stateId));
		if(!empty($cityData)){
			foreach ($cityData as $value) {
				if($value['iCityId'] == $selectedId){
					$op .= "<option selected value='".$value['iCityId']."'>".$value['vCity']."</option>";
				}else{
					$op .= "<option value='".$value['iCityId']."'>".$value['vCity']."</option>";
				}
			}
		}else{
			$op .= "<option value=''>Please Select State first</option>";
		}
		echo $op;exit;
	}

	public function __getConfiguration($configName = ''){
		$where = array('eStatus' => 'Active');
		$tableField = 'vName,vValue';
		if(!empty($configName)){
			$where['vName'] = $configName;
			$tableField = 'vValue';
		}
		$value = $this->Setting_model->__getwheredata('configurations',$tableField,$where,'iSettingId','desc',NULL);
		return empty($configName) ? $value : $value[0]['vValue'];
	}

	public function addMoneyToUserWallet($eUserType, $iUserId, $amount, $message='', $iBookingId = '', $eFor = 'Deposit',$iReferrId = ''){

	    if( !empty($amount) && !empty($iUserId) ){

	    	$message = empty($message) ? '#LBL_WALLET_AMOUNT_ADD_SUCCESS#' : $message;
	    	$amount = number_format( (float)$amount, 2, '.', '' );
	    	$tbl = "register_user";
		    $primaryId = "iUserId";
		    if($eUserType == "Agent"){
		    	$tbl = "register_agent";
		    	$primaryId = "iAgentId";
		    }
	    	$where = array($primaryId => $iUserId);
	    	$checkUser = $this->Setting_model->__getsingledata($tbl,$primaryId,$where);
	    	if($checkUser[0][$primaryId] > 0){
	    		$walletData = array(
	    			'iUserId' => $checkUser[0][$primaryId],
	    			'eUserType' => $eUserType,
	    			'iBalance' => $amount,
	    			'eType' => 'Credit',
	    			'iBookingId'=>$iBookingId,
	    			'eFor' => $eFor,
	    			'iReferrId' => $iReferrId,
	    			'tDescription' => $message,
	    			'ePaymentStatus' => 'Unsettelled',
	    			'dDate' => date('Y-m-d h:i:s'),
	    			'iTransactionId' => $this->getWalletTransId()
	    		);
	    		$insert = $this->Setting_model->__add_single_data('user_wallet',$walletData);
	    		return $insert;
	    	}
	    }
	}

	public function sendPushFromWebtoDroid($registatoin_ids, $MSGTITLE, $MSGBODY){

		$FIREBASE_API_ACCESS_KEY = $this->__getConfiguration('FIREBASE_API_ACCESS_KEY');
		$registrationIds = array( $registatoin_ids );

		$message = array
					(
						'title'		=> $MSGTITLE,
	                    'body'     => $MSGBODY,
						'vibrate'	=> 1,
						'sound'		=> 1,
						/*'largeIcon'	=> 'large_icon',
						'smallIcon'	=> 'small_icon'*/
					);
		$fields = array(
	        'registration_ids' => $registrationIds,
	        'click_action' => ".MainActivity",
	        'priority' => "high",
	        'data' => $message
	    );

	    $finalFields = json_encode($fields, JSON_UNESCAPED_UNICODE);

	    $headers = array(
	        'Authorization: key=' . $FIREBASE_API_ACCESS_KEY,
	        'Content-Type: application/json',
	    );

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $finalFields);

	    $response = curl_exec($ch); 
	    
	    $responseArr = json_decode($response);
	    $success = $responseArr->success;
	    curl_close($ch);
	    
	    return $success;
	}

	public function isEligibleReferral($userType,$userID,$referralId){

		//checking is referred already or not
		$query = "SELECT * FROM `user_wallet` WHERE eFor = 'Referrer' AND eUserType = '$userType' AND iReferrId = '$referralId' AND iUserId = '$userID'";

		$runQ = $this->Setting_model->__runCustomQuery($query);
		if(!empty($runQ)){
			return $runQ;
		}else{
			return "";
		}

	}
}
