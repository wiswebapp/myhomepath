<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webservice_model extends MY_Model {

	public function __construct(){
        parent::__construct();
    }

    public function getGeneralData()
    {
    	$today = date('Y-m-d');
    	$startDate = $today;
    	$endDate = $today;

    	$startTime = date('H:i:s');
    	$endTime = '24:00:00';
    	
    	//For Getting Most 3 Routes
    	$top3RouteQ = 'SELECT ro.*,bu.vBusName,bu.vBusRegnum,bu.vAmenities AS busAmenities,(ro.iAvailableSeat + ro.iAvailableSleeper) AS totalAvailbleSeat FROM `routes` ro
    	    				LEFT JOIN buses bu
    	    				ON ro.iBusId = bu.iBusId
    						WHERE (ro.`eFrequency` = "Daily" OR  ro.eFrequency = "Once" )
    	                    AND ro.`dRouteDate` BETWEEN "'.$startDate.'" AND "'.$endDate.'"
    						AND TIME(ro.tFromTime) BETWEEN "'.$startTime.'" AND "'.$endTime.'"
    						AND (ro.iAvailableSeat + ro.iAvailableSleeper) > 0 
    						ORDER BY ro.iAvailableSeat DESC, ro.iAvailableSleeper LIMIT 3';
    	$returnArr['top3Route'] = $this->db->query($top3RouteQ)->result_array();

    	//For Getting offers
    	$getPromoQ = "SELECT * FROM `promocode` WHERE eStatus = 'Active' AND iUsageLimit > 0 AND eValidityType = 'Permanent'";
    	$returnArr['coupons'] = $this->db->query($getPromoQ)->result_array();

        //For Getting Advertisement Banner
        $adData = $this->db->select('*')->from('advertise_banners')->get()->result_array();
        
        $returnArr['busAdOuter'] =  webimagesPath('advertise/'.$adData[0]['vBannerImage']);
        $returnArr['busAdInner'] =  webimagesPath('advertise/'.$adData[1]['vBannerImage']);
        $returnArr['tourAdOuter'] = webimagesPath('advertise/'.$adData[2]['vBannerImage']);
        $returnArr['tourAdInner'] = webimagesPath('advertise/'.$adData[3]['vBannerImage']);
    	return $returnArr;
    }
    public function logoutUser($userId,$userType,$postData){

    	$table = "register_agent";
		$primary = "iAgentId";
		if($userType == "User"){
			$table = "register_user";
			$primary = "iUserId";
		}

		$logout = $this->__update_single_data($table,$postData,array($primary=>$userId));
		return $logout;

    }
}