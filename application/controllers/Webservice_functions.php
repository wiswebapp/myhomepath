<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('default_socket_timeout', 10);
ini_set('memory_limit', '-1');


class Webservice_functions extends MY_Controller {

	public function __construct(){
        parent::__construct();

        $this->load->model('Validate_model');
    }

    public function allTypeList(){
        //All Types Are defined here
        $typeArray = array('updateuserprofile','loadAvailbleRoute','getFromRouteLocations','getSingleRouteData','getwalletreport','getbuspoints','confirmBusBooking','getBookingDetails','applyPromocode','getBookingHistory','bookingTimeOut','sendContactQuery','changepassword','generalData','walletconfirmBusBooking','loadAvailbleTour','getSingleTourData','submitTourQuery','getDetailOrg','verifyOrgDoc','updateOrgProfile','cronDetail','makePayment','getOrders','updateuserprofile','addOrderJob','getJobOrderReport','acceptOrder','getSingleOrderDetail','completeOrder','confirmPayment');

        return $typeArray;
    }

    public function sessionOut($message = ''){

        $message = !empty($message) ? $message : 'SESSION_OUT';

        if($message == 'MISSING_PARAM'){
            $message = "Something Went Wrong in Parameters";
        }

        $returnArr['Action'] = "0";
        $returnArr['message'] = $message;
        echo json_encode($returnArr);
        exit;
        
    }

}