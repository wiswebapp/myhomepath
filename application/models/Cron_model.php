<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_model extends MY_Model {

	public function __construct(){
        parent::__construct();
    }

    public function getPendingRouteInformation(){

        $CRON_SCRIPT_TIME_F = $this->db->query("SELECT vValue FROM configurations WHERE vName = 'CRON_SCRIPT_TIME'")->result_array();
        $CRON_SCRIPT_TIME = $CRON_SCRIPT_TIME_F[0]['vValue'];
        $result = array();

        $today = date('Y-m-d');
        $startTime = '00:00:00';
        $endTIme = date('h:i:s',strtotime($CRON_SCRIPT_TIME.' minutes'));

		/*$whereCondition = array(
			'eBookingStatus'=>'Pending',
			'tBookingDate'=>date('Y-m-d'),
		);
        $this->db->where("DATE(tBookingRequest) = '".$today."' ");
        $this->db->where("TIME(tBookingRequest) BETWEEN '".$startTime."' AND '".$endTIme."'");
		$result = $this->__getwheredata('bookings','*',$whereCondition,'iBookingId','DESC',NULL);*/
        $cronQuery = "SELECT * FROM `bookings` WHERE (DATE(tBookingRequest) = '".$today."' AND TIME(tBookingRequest) BETWEEN '".$startTime."' AND '".$endTIme."') OR (DATE(tBookingRequest) < '".$today."') AND `eBookingStatus` = 'Pending' ORDER BY `iBookingId` DESC";
        /*echo $this->db->last_query();exit;*/
        $runQ = $this->db->query($cronQuery);
        if($runQ->num_rows() > 0){
            $result = $runQ->result_array();
        }
		return $result;
    }


    public function runCronScript()
    {
    	$getDailyRoute = $this->getPendingRouteInformation();
        $update = 0;
        if(count($getDailyRoute) > 0){
            //update past route as finished
            
            foreach ($getDailyRoute as $rtData) {
                
                $iRouteId = $rtData['iRouteId'];
                $eSeatType = $rtData['eSeatType'];//Seater or Sleeper
                $eSeatType = ucfirst(strtolower($eSeatType));
                $iBookingId = $rtData['iBookingId'];
                $ssql = "";
                
                if($eSeatType == "Seater" || $eSeatType == "Seat"){
                    $fieldName = "iAvailableSeat";
                }else{
                    $fieldName = "iAvailableSleeper";    
                }

                //Removing Pending Bookings
                $condition = array('iBookingId'=>$iBookingId);
                $removeBooking = $this->__delete_single_data('bookings',$condition);

                if($removeBooking){
                    //Free up the seats that is allocated to pending bookings
                    $query="UPDATE `routes` SET $fieldName = $fieldName + 1 $ssql WHERE iRouteId = '".$iRouteId."'";
                    $this->db->query($query);
                    $update = $update + 1;
                }
            }
        }
        return $update;
    }

}
?>