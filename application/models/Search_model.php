<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends MY_Model {

    public function __construct(){
        parent::__construct();
    }

    public function getBusSearchData($ssql = '',$journeyDate = ''){

        $today = date('Y-m-d');
        $ssql = empty($ssql) ? ' 1=1' : $ssql;

    	$startDate = $today;
    	$endDate = $today;
    	if(!empty($journeyDate)){
    		$startDate = toDate($journeyDate,'Y-m-d');
    		$endDate = toDate($journeyDate,'Y-m-d');
    	}

        $startTime = date('H:i:s');
        if($startDate != $today){
    	   $startTime = '00:00:00';
        }
    	$endTime = '24:00:00';

    	$query = 'SELECT ro.*,bu.vBusName,bu.vBusRegnum,bu.vAmenities AS busAmenities,(ro.iAvailableSeat + ro.iAvailableSleeper) AS totalAvailbleSeat FROM `routes` ro
    				LEFT JOIN buses bu
    				ON ro.iBusId = bu.iBusId
					WHERE '.$ssql.' 
					AND (ro.`eFrequency` = "Daily" OR  ro.eFrequency = "Once" )
                    AND ro.`dRouteDate` BETWEEN "'.$startDate.'" AND "'.$endDate.'"
					AND TIME(ro.tFromTime) BETWEEN "'.$startTime.'" AND "'.$endTime.'"
					AND (ro.iAvailableSeat + ro.iAvailableSleeper) > 0 
					ORDER BY ro.tCreatedDate DESC';
    	$result = $this->db->query($query);
    	$data['rows'] = $result->num_rows();
    	$data['result'] = $result->result_array();
        
    	return $data;
    }

    public function getTourSearchData($ssql = '',$journeyDate=''){

        if(empty($journeyDate)){
            $journeyDate = date('Y-m-d');
        }
        $ssql = empty($ssql) ? 'AND 1=1' : $ssql;

        //$tempField = "iTourId,vToureId,tCreatedDateTime,tCreatedDate,vTourName,vBoardingPlace,vDroppingPlace,vBoardingTime,vDroppingTime,dTourDate";
        $query = "SELECT * FROM `tours` WHERE eStatus = 'Active' AND iAvailablePerson > 0 AND dTourDate >= '".$journeyDate."' $ssql ORDER BY dTourDate DESC";
        /*echo $query;exit;*/
        $result = $this->db->query($query);

        $data['rows'] = $result->num_rows();
        if($data['rows'] > 0){

            //loop for getting image
            $tourData = $result->result_array();

            foreach ($tourData as $key=>$trvalue) {

                $primaryImage = "no-tour-image.jpg";
                $iTourId = $trvalue['iTourId'];
                $imageData = $this->db->select('iTourId,vImageName,isPrimary')
                            ->from('tours_images')
                            ->where('iTourId',$iTourId)
                            ->get()->result_array();
                $tourData[$key]['tourImage'] = $imageData;

                foreach ($imageData as $imgvalue) {
                    if($imgvalue['isPrimary'] == "Yes"){
                        $primaryImage = $imgvalue['vImageName'];
                    }
                }
                //printr($imageData);
                //if no primary than took default
                if(empty($primaryImage)){
                    $primaryImage = $imageData[0]['vImageName'];
                }

                //for primary image
                $tourData[$key]['primaryImage'] = $primaryImage;

            }
            
            $data['result'] = $tourData;
        }else{
            $data['result'] = $result->result_array();
        }
        //printr($data);
        return $data;
    }

    public function getFromSuggestion($arrayMode = FALSE){
        
        $query = "SELECT DISTINCT(vFromPlace) FROM `routes`";
        $result = $this->db->query($query);
        $resultA = $result->result_array();

        if($arrayMode == FALSE){
            $string = "";
            foreach ($resultA as $value) {
                 $string .= '"'.$value['vFromPlace'].'", ';
            }
            $string = rtrim($string,", ");
        }else{
            $string = $resultA;
        }
        return $string;
    }
    
    public function getToSuggestion($arrayMode = FALSE){
        
        
        $query = "SELECT DISTINCT(vToPlace) FROM `routes`";
        $result = $this->db->query($query);
        $resultA = $result->result_array();

        if($arrayMode == FALSE){
            $string = "";
            foreach ($resultA as $value) {
                $string .= '"'.$value['vToPlace'].'", ';
            }
            $string = rtrim($string,", ");
        }else{
            $string = $resultA;
        }
        return $string;
    }

    public function getAmenities($amenitiesId){

        $amenitiesIdA = explode(',', $amenitiesId);

        if( is_array($amenitiesIdA) ){
            for ($i=0; $i < count($amenitiesIdA); $i++) { 
                $this->db->or_where('iAmenitiesId',$amenitiesIdA[$i]);
            }
        }else{
            $this->db->where('iAmenitiesId',$amsenitiesId);
        }

        $query = $this->db->select('*')
                            ->from('amenities')
                            ->where('eStatus','Active')
                            ->get();
        //echo $this->db->last_query();exit;
        $result = $query->result_array();
        return $result;
    }

    public function getBoardingData($routeId){

        $query = $this->db->select('*')
                            ->from('bus_points')
                            ->where(array('iRouteId'=>$routeId,'eType'=>'Boarding'))
                            ->get();
        return $query->result_array();
    }

    public function getDroppingData($routeId){

        $query = $this->db->select('*')
                            ->from('bus_points')
                            ->where(array('iRouteId'=>$routeId,'eType'=>'Dropping'))
                            ->get();
        return $query->result_array();
    }
    
    /*public function getSingleRouteData($routeId){

        $query = 'SELECT ro.*,bu.vBusName,bu.vBusRegnum,(ro.iAvailableSeat + ro.iAvailableSleeper) AS totalAvailbleSeat FROM `routes` ro
                    LEFT JOIN buses bu
                    ON ro.iBusId = bu.iBusId
                    WHERE ro.iRouteId = "'.$routeId.'" AND ro.eStatus = "Active"
                    AND (ro.`eFrequency` = "Daily" OR  ro.eFrequency = "Once" )
                    AND (ro.iAvailableSeat + ro.iAvailableSleeper) > 0';
        $routeBusData = $this->db->query($query);

        if($routeBusData->num_rows() > 0){

            $routeBusData = $routeBusData->result_array();
            $result['routeData'] = $routeBusData[0];
            $result['amenities'] = $this->Booking_model->getRouteDetails($routeId);

        }else{
            $result['routeData'] = 0;
        }
        return $result;
    }*/
}


?>
