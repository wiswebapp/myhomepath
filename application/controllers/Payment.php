<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

class Payment extends MY_Controller {

	public $APP_PAYMENT_METHOD;

	public function __construct(){
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->model('Validate_model');
    }

	public function index(){
		return redirect(base_url());
	}

	public function getPaymentConfiguration($APP_PAYMENT_METHOD){

		if($APP_PAYMENT_METHOD == "Payumoney"){

			$return['PAYMENT_ENABLE_SANDBOX'] = $this->__getConfiguration('PAYMENT_ENABLE_SANDBOX');
			$return['PAYUMONEY_MERCHANT_KEY'] = $this->__getConfiguration('PAYUMONEY_MERCHANT_KEY');
			$return['PAYUMONEY_MERCHANT_MID'] = $this->__getConfiguration('PAYUMONEY_MERCHANT_MID');
			
			return $return;
		}
	}

	public function paymentRequest($orderId){
		
		if(!empty($orderId)){

			$APP_PAYMENT_METHOD = $this->__getConfiguration('APP_PAYMENT_METHOD');
			
			$data['getwayData'] = $this->getPaymentConfiguration($APP_PAYMENT_METHOD);
			$data['orderData'] = $this->Booking_model->getOrderFullDetail($orderId);
			$this->load->view('payment_request',compact('data'));
		}
	}

	public function paymentResponse(){
		
		$postdata = $_POST;		
		$msg = '';
		$PAYUMONEY_MERCHANT_KEY = $this->__getConfiguration('PAYUMONEY_MERCHANT_KEY');
		if (isset($postdata ['key'])) {
			$key				=   $postdata['key'];
			$salt				=   $PAYUMONEY_MERCHANT_KEY;
			$txnid 				= 	$postdata['txnid'];
		    $amount      		= 	$postdata['amount'];
			$orderId  		= 	$postdata['productinfo'];
			$firstname    		= 	$postdata['firstname'];
			$email        		=	$postdata['email'];
			$udf5				=   $postdata['udf5'];
			$mihpayid			=	$postdata['mihpayid'];
			$status				= 	$postdata['status'];
			$resphash				= 	$postdata['hash'];
			$iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : '';

			//Calculate response hash to verify	
			$keyString 	  		=  	$key.'|'.$txnid.'|'.$amount.'|'.$orderId.'|'.$firstname.'|'.$email.'|||||'.$udf5.'|||||';
			$keyArray 	  		= 	explode("|",$keyString);
			$reverseKeyArray 	= 	array_reverse($keyArray);
			$reverseKeyString	=	implode("|",$reverseKeyArray);
			$CalcHashString 	= 	strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString));
			
			
			if ($status == 'success'  && $resphash == $CalcHashString) {
				
				//ADd Data to payment
				$paymentData['iUserId'] = $iUserId;
				$paymentData['vTXNID'] = $txnid;
				$paymentData['tPaymentMsg'] = $postdata['udf5'];
				$paymentData['vPaymentStatus'] = "Paid";
				$paymentData['iOrderId'] = $orderId;
				$paymentData['iAmount'] = $amount;
				$paymentData['tPaymentDetails'] = json_encode($postdata);
				$paymentData['vPaymentMode'] = "ONLINE";
				$paymentData['dPaymentDate'] = date('Y-m-d h:i:s');
				$updtOrdr = $this->Booking_model->__add_single_data('payments',$paymentData);
				//Update Order
				$updateOrderArr['eStatus'] = "Placed";
				$cond['vOrderId'] = $orderId;
				$updtOrdr = $this->Booking_model->__update_single_data('orders',$updateOrderArr,$cond);

				$returnArr['Action'] = 1;
				$returnArr['message'] = $paymentData;
			}
			else {
				$returnArr['Action'] = 0;	
			} 
			echo json_encode($returnArr);
			exit;
		}
	}

	public function thank_you(){
		$data['msg'] = 'Success';
		$this->load->view('payment_response',compact('data'));
	}
	public function failed(){
		$data['msg'] = 'Failed';
		$this->load->view('payment_response',compact('data'));
	}
	public function response_webview()
	{
		exit;
	}
}