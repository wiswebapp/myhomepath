<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('default_socket_timeout', 10);
ini_set('memory_limit', '-1');

include_once('Webservice_functions.php');

class Webservice_organization extends Webservice_functions {

	/*public function signupOrganization(){

    	$vOrgName = isset($_REQUEST['vOrgName']) ? cleanString($_REQUEST['vOrgName']) : '';
    	$vUserMobile = isset($_REQUEST['vUserMobile']) ? cleanString($_REQUEST['vUserMobile']) : '';
    	$vUserEmail = isset($_REQUEST['vUserEmail']) ? cleanString($_REQUEST['vUserEmail']) : '';
    	$vPassword = isset($_REQUEST['vPassword']) ? cleanString($_REQUEST['vPassword']) : '';
    	$vFirebaseDeviceToken = isset($_REQUEST['vFirebaseDeviceToken']) ? cleanString($_REQUEST['vFirebaseDeviceToken']) : '';

    	$postData = $_REQUEST;
    	unset($postData['type']);

    	$isEmailExist = $this->Validate_model->isEmailExist('organization','vUserEmail',$vUserEmail,TRUE);
    	if( empty($isEmailExist) ){
    		$isMobileExist = $this->Validate_model->isEmailExist('organization','vUserMobile',$vUserMobile,TRUE);
    		if( empty($isMobileExist) ){
	    		$postData['vPassword'] = encryptPassword($vPassword);
	    		$postData['tRegDate'] = date('Y-m-d');
				$postData['eStatus'] = "InActive";
				$storeId = $this->getStoreId($vUserMobile);
				$postData['vOrgId'] = $storeId;
				$adduser = $this->Validate_model->__add_single_data('organization',$postData);
				//===================================Sending Email
				$emailData['vOrgName'] = $vOrgName;
				$emailData['vUserEmail'] = $vUserEmail;
				$emailData['iOrgId'] = $storeId;
				$emailMessage = $this->getEmailTemplate('TEMPLATE_ORGANIZATION_REGISTER',$emailData);
				$NOREPLY_EMAIL = $this->__getConfiguration('NOREPLY_EMAIL');
				$ADMIN_EMAIL = $this->__getConfiguration('ADMIN_EMAIL');
				//Sending Mail To User
				$this->sendEmailFromSystem($NOREPLY_EMAIL,APP_TITLE, $vUserEmail, $emailMessage['subject'], $emailMessage['message']);
				//Sending Mail To Admin
				$this->sendEmailFromSystem($NOREPLY_EMAIL,APP_TITLE, $ADMIN_EMAIL, $emailMessage['subject'], $emailMessage['message']);
				
				$returnArr['Action'] = "1";
			   	$returnArr['message'] = "Registred Successfully";
			}else{
				$returnArr['Action'] = "2";
	   			$returnArr['message'] = "User with this mobile is already Exist.";	
			}
    	}else{
    		$returnArr['Action'] = "2";
	   		$returnArr['message'] = "User with this email is already Exist.";
    	}
    	echo json_encode($returnArr);
    	exit;
	}*/
	public function signinOrganization(){
    	
    	$username = isset($_REQUEST['vUserEmail']) ? cleanString($_REQUEST['vUserEmail']) : '';
	    $password = isset($_REQUEST['vPassword']) ? cleanString($_REQUEST['vPassword']) : '';
	    $vFirebaseDeviceToken = isset($_REQUEST['vFirebaseDeviceToken']) ? cleanString($_REQUEST['vFirebaseDeviceToken']) : '';

	    //$userTableData
	    $authenticate = $this->Validate_model->isOrganizationValid( $username,$password );

		if( $authenticate['Action'] == TRUE){
			
			if($authenticate['Msg'] == "LOGIN_SUCCS"){
				//update tsession id
				$vSessionId = session_id().time();
				$userId = $authenticate[0]['iOrgId'];
				$dataUpdate = array();
				$fullName = $authenticate[0]['vUserName'];
				$vEmail = $authenticate[0]['vUserEmail'];
				$vPhone = $authenticate[0]['vUserMobile'];
				$dataUpdate['vSessionId'] = $vSessionId;
				$dataUpdate['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
				$dataUpdate['eLogout'] = 'No';
				$condition['eStatus'] = "Active";
				$condition['iOrgId'] = $userId;
				$update = $this->Validate_model->__update_single_data('organization',$dataUpdate,$condition);
				
				if($update > 0){
					$returnArr['Action'] = "1";
					$returnArr['message'] = array(
												'vSessionId' => $vSessionId,
												'GeneralMemberId' => $userId,
												'GeneralUserType' => 'Organization',
												'GeneralUserName' => $fullName,
												'GeneralUserEmail' => $vEmail,
												'GeneralUserPhone' => $vPhone,
											);
				}else{
					$returnArr['Action'] = "0";
					$returnArr['message'] = "Something Went Wrong";
				}
			}else{
				$returnArr['Action'] = "2";
				$returnArr['message'] = "Seems like user is blocked or inactive";
			}
		}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "Invalid Username/Password";
		}
		    
	    echo json_encode($returnArr);
	    exit;
    }

    public function verifyOrgMobile(){
    	
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
		$vUserMobile = isset($_REQUEST['vUserMobile']) ? $_REQUEST['vUserMobile'] : '';
		
		if(!empty($vUserMobile)){

			$data['eMobileVerified'] = "Yes";
			$condition['iOrgId'] = $GeneralMemberId;
			$verifyMobile = $this->Validate_model->__update_single_data('organization',$data,$condition);
			if($verifyMobile){
				$returnArr['Action'] = "1";
				$returnArr['message'] = "Successfully verified mobile number";
			}else{
				$returnArr['Action'] = "0";
				$returnArr['message'] = "Something Went Wrong.";
			}
			echo json_encode($returnArr);
	    	exit;
		}
    }
    public function verifyOrgDoc(){
    	
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
		$tconfig = $this->tconfig();
		
		if(!empty($_FILES)){

			//Image Upload
			if(!empty($_FILES['vImgName']['name'])){
				$pathToUpload = $tconfig['root_path'].'webimages/organization/document/';
				$generatedFileName = "Doc_".strtolower($GeneralUserType)."_".$GeneralMemberId."";
				$upload = $this->_solo_file_upload('vImgName',$pathToUpload,'',$generatedFileName);
				$fileName = $upload['message']['upload_data']['raw_name'].$upload['message']['upload_data']['file_ext'];
				if($fileName){
					$returnArr['Action'] = "1";
					$returnArr['message'] = "Documents Are Uploaded Successfully";
				}else{
					$returnArr['Action'] = "0";
					$returnArr['message'] = "Something Went Wrong.";
				}
			}
			echo json_encode($returnArr);
	    	exit;
		}
    }
    public function updateOrgProfile(){

    	$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
    	$vOrgName = isset($_REQUEST['vOrgName']) ? $_REQUEST['vOrgName'] : '';
    	$vAddress = isset($_REQUEST['vAddress']) ? $_REQUEST['vAddress'] : '';
    	$vCity = isset($_REQUEST['vCity']) ? $_REQUEST['vCity'] : '';
    	//GeneratingCityId
    	$cityIdQ = $this->Profile_model->getCityList(array('vCity'=>$vCity));
    	$cityId = $cityIdQ[0]['iCityId'];
    	$vZipCode = isset($_REQUEST['vZipCode']) ? $_REQUEST['vZipCode'] : '';
    	$vStartTime = isset($_REQUEST['vStartTime']) ? $_REQUEST['vStartTime'] : '';
    	$vEndtTime = isset($_REQUEST['vEndtTime']) ? $_REQUEST['vEndtTime'] : '';
    	$vLocationLat = isset($_REQUEST['vLocationLat']) ? $_REQUEST['vLocationLat'] : '';
    	$vLocationLong = isset($_REQUEST['vLocationLong']) ? $_REQUEST['vLocationLong'] : '';
    	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';//show or update
    	$condition = array('iOrgId' => $GeneralMemberId);
    	
		$returnArr['Action'] = 1;
    	if($action == "update"){
    		
    		$dataUpdate['vOrgName'] = $vOrgName;
			$dataUpdate['vAddress'] = $vAddress;
			$dataUpdate['vCountry'] = $cityIdQ[0]['iCountryId'];
			$dataUpdate['vState'] = $cityIdQ[0]['iStateId'];
			$dataUpdate['vCity'] = $cityId;
			$dataUpdate['vZipCode'] = $vZipCode;
			$dataUpdate['vStartTime'] = date('H:i:s',strtotime($vStartTime));
			$dataUpdate['vEndtTime'] = date('H:i:s',strtotime($vEndtTime));
			$dataUpdate['vLocationLat'] = $vLocationLat;
			$dataUpdate['vLocationLong'] = $vLocationLong;
			$update = $this->Profile_model->__update_single_data('organization',$dataUpdate,$condition);
    	}

    	$storeData = $this->Profile_model->getProfileDetail($GeneralUserType,$GeneralMemberId)[0];
    	//Check For Store Information
    	$chk = array($storeData['vOrgName'],$storeData['vAddress'],$storeData['vCity'],$storeData['vZipCode'],$storeData['vStartTime'],$storeData['vEndtTime']);

    	if(checkEmptyStatus($chk)){
    		$dataUpdat['eStoreInfoVerified'] = 'Yes';
    	}else{
    		$dataUpdat['eStoreInfoVerified'] = 'No';
    	}    	
    	$update = $this->Profile_model->__update_single_data('organization',$dataUpdat,$condition);
    	//GeneratingCityId
    	$cityIdQ = $this->Profile_model->getCityList(array('iCityId'=>$storeData['vCity']));
    	$storeData['vCity'] = $cityIdQ[0]['vCity'];
    	
    	$returnArr['message'] = $storeData;
    	echo json_encode($returnArr);
	    exit;
    }

    public function addOrderJob(){
    	$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
    	$vLat = isset($_REQUEST['vLat']) ? $_REQUEST['vLat'] : '';
    	$vLong = isset($_REQUEST['vLong']) ? $_REQUEST['vLong'] : '';
    	$dDate = isset($_REQUEST['dDate']) ? $_REQUEST['dDate'] : '';
    	$eLogType = isset($_REQUEST['eLogType']) ? $_REQUEST['eLogType'] : '';
    	$availablity = "Not Available";//Not Working On Job
    	if($eLogType == "Started"){
    		$availablity = "Available";//Working On Job
    	}

    	$returnArr['Action'] = 0;
	    $returnArr['message'] = "Somehting Went Wrong !";

    	if(!empty($vLat) && !empty($vLat) && !empty($dDate) && !empty($eLogType)){
	    	$dataAdd['iProviderId'] = $GeneralMemberId;
	    	$dataAdd['dDate'] = toDate($dDate,'Y-m-d');
	    	$dataAdd['vLat'] = $vLat;
	    	$dataAdd['vLong'] = $vLong;
	    	$dataAdd['eLogType'] = $eLogType;
	    	$adduser = $this->Validate_model->__add_single_data('provider_logs',$dataAdd);
	    	$updatStts['vJobStatus'] = $availablity;
	    	$updatStts['vLocationLat'] = $vLat;
	    	$updatStts['vLocationLong'] = $vLong;
	    	$cond['iOrgId'] = $GeneralMemberId;
	    	$update = $this->Validate_model->__update_single_data('organization',$updatStts,$cond);
	    	if($adduser){
	    		$returnArr['Action'] = 1;
	    		$returnArr['message'] = "Record Updated Successfully .!";
	    	}else{
	    		$returnArr['Action'] = 0;
	    		$returnArr['message'] = "Somehting Went Wrong !";
	    	}
	    }
    	echo json_encode($returnArr);
	    exit;
    }


    public function getJobOrderReport(){
    	
    	$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
    	$dStartDate = isset($_REQUEST['dStartDate']) ? $_REQUEST['dStartDate'] : '';
    	$dEndDate = isset($_REQUEST['dEndDate']) ? $_REQUEST['dEndDate'] : '';
    	
    	$reslt = $this->Common_front_model->getLogReport($GeneralMemberId,$dStartDate,$dEndDate);
    	if(!empty($reslt)){
    		$returnArr['Action'] = 1;
	    	$returnArr['message'] = $reslt;
	    }else{
	    	$returnArr['Action'] = 0;
	    	$returnArr['message'] = "Somehting Went Wrong !";
	    }
	    echo json_encode($returnArr);
	    exit;
    }

}
