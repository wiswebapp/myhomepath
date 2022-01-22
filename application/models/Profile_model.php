<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends MY_Model {

	public function __construct(){
        parent::__construct();
    }

    public function getProfileDetail($userType,$userId){

        if($userType == 'User'){
            $query = "SELECT org.*, country.vCountry As vCountryName, state.vState As vStateName, city.vCity As vCityName FROM register_user org LEFT JOIN country ON org.vCountry = country.iCountryId LEFT JOIN state ON org.vState = state.iStateId LEFT JOIN city ON org.vCity = city.iCityId WHERE org.iUserId = $userId";

        }else{
            $query = "SELECT org.*, country.vCountry As vCountryName, state.vState As vStateName, city.vCity As vCityName FROM organization org LEFT JOIN country ON org.vCountry = country.iCountryId LEFT JOIN state ON org.vState = state.iStateId LEFT JOIN city ON org.vCity = city.iCityId WHERE org.iOrgId = $userId";
        }
        $getProfileData = $this->__runCustomQuery($query);
        //printr($getProfileData);
        return $getProfileData;
        
    }

    public function updateProfile($userType,$userId,$postData ){

        $tablename = "register_agent";
        $primaryKey = "iAgentId";
        if($userType == "User"){
            $tablename = "register_user";
            $primaryKey = "iUserId";
        }

        $where = array($primaryKey => $userId);
        $updateProfile = $this->__update_single_data($tablename,$postData,$where);
        
        return TRUE;
    }
    public function getBookingData( $userType,$userId,$ssql='' ){

        if(!empty($ssql)){
            $ssql = $ssql." AND ";
        }
    	$ssql2 = " eBookingBy = '".$userType."' AND iUserId = '".$userId."' AND eBookingStatus != 'Pending'";
    	$query = "SELECT *,COUNT(*) AS TotalBookings FROM `bookings` bo LEFT JOIN routes ro ON bo.iRouteId = ro.iRouteId LEFT JOIN buses bu ON bo.iBusId = bu.iBusId WHERE  $ssql $ssql2 GROUP BY bo.vBookingNo ORDER BY bo.iBookingId DESC $limitQ";
    	
    	$dataR = $this->db->query($query)->result_array();
    	return $dataR;
    }
    public function getSingleBookingData($bookingId){
        if(!empty($bookingId)){
            $query = "SELECT * FROM `bookings` WHERE vBookingNo = '".$bookingId."' ";
            $dataR = $this->db->query($query)->result_array();
            return $dataR;    
        }
    }
    public function getWalletData( $userType,$userId,$ssql='' ){

        if(empty($ssql)){
            $ssql = " 1=1 ";
        }
        $query = "SELECT * FROM user_wallet WHERE iUserId = $userId AND eUserType = '$userType' AND $ssql ORDER BY iUserWalletId ASC";
        $dataR = $this->db->query($query)->result_array();
        return $dataR;
    }
}