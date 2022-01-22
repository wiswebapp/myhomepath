<?php  
	
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	

	function generateHash($merchantKey,$transId,$amount,$description,$name,$email,$udf5 = 'BOLT_KIT_PHP7',$merchantPass){
		$data = json_decode(file_get_contents('php://input'));
		$hash=hash('sha512', $merchantKey.'|'.$transId.'|'.$amount.'|'.$description.'|'.$name.'|'.$email.'|||||'.$udf5.'||||||'.$merchantPass);

	    return $hash;
	}
	/*=======================================URL=======================================*/
	function admin_url($url = ''){
		$parameters = empty($url) ? ADMIN_FOLDER : ADMIN_FOLDER."/".$url;
		return base_url($parameters);
	}
	function assets($url = ''){
		$parameters = empty($url) ? base_url('assets/') : base_url('assets/'.$url);
		return $parameters;
	}
	function webimagesPath($url = ''){
		$parameters = empty($url) ? base_url('webimages/') : base_url('webimages/'.$url);
		return $parameters;
	}
	function admin_assets($url = ''){
		$parameters = empty($url) ? base_url('assets/admin/') : base_url('assets/admin/'.$url);
		return $parameters;
	}
	function limitCharacter($string , $length = 50){
		if(strlen($string) > 50){
			$string = substr($string , 0, $length);
			$string = $string . "...";
		}
		return $string;
	}
	/*=======================================GENERAL=======================================*/
	function get_client_ip() {
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
	function covertToInr($num) {
	    $explrestunits = "" ;
	    if(strlen($num)>3) {
	        $lastthree = substr($num, strlen($num)-3, strlen($num));
	        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
	        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
	        $expunit = str_split($restunits, 2);
	        for($i=0; $i<sizeof($expunit); $i++) {
	            // creates each of the 2's group and adds a comma to the end
	            if($i==0) {
	                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
	            } else {
	                $explrestunits .= $expunit[$i].",";
	            }
	        }
	        $thecash = $explrestunits.$lastthree;
	    } else {
	        $thecash = $num;
	    }
	    return $thecash; // writes the final format where $currency is the currency symbol.
	}
	
	function printr($array,$message='',$exit = 'Y'){
		$message = empty($message) ? '' : $message.'<hr>';
		echo $message."<pre>";
		print_r($array);
		if($exit == "Y"){
			exit;
		}
	}
	function toDecimalPoint($value,$decimalValue = 2)
	{
		if( is_numeric($value) ){
			return number_format((float)$value, 2, '.', '');
		}
	}
	function convertMD5($key)
	{
		$key = trim($key);
		$op = $key;
		$keyLen = strlen($op);
		if($keyLen != 32){
			$op = MD5($key);
		}
		return $op;
	}
	function toDate($timeStampVal,$format = ''){
		$format = !empty($format) ? trim($format) : 'd-M-Y';
		$newDate = date($format,strtotime($timeStampVal));
		return $newDate;
	}
	function toTime($timeStampVal,$format = ''){
		$format = !empty($format) ? trim($format) : 'h:i A';
		$newDate = date($format,strtotime($timeStampVal));
		return $newDate;
	}
	function cleanString($value){
		$value = trim($value);
		return $value;
	}
	function cleantab($value){
      $value = strtolower($value);
      $value = str_replace(' ', '', $value);
      return $value;
    }
    function cleanLabelCode($labelCode){
		$pat = '/\#([^\"]*?)\#/';
		$data = preg_match($pat, $labelCode, $tDescription_value);
		return $tDescription_value;        
	}
	function timeDiff($starttime, $endtime){

	    $timespent = strtotime( $endtime)-strtotime($starttime);
	    $days = floor($timespent / (60 * 60 * 24)); 
	    $remainder = $timespent % (60 * 60 * 24);
	    $hours = floor($remainder / (60 * 60));
	    $remainder = $remainder % (60 * 60);
	    $minutes = floor($remainder / 60);
	    $seconds = $remainder % 60;
	    $TimeInterval = '';
	    if($hours < 0) $hours=0;
	    if($hours != 0){
	        $TimeInterval = ($hours == 1) ? $hours.' hour' : $hours.' hours';
	    }
	    if($minutes < 0) $minutes=0;
	    if($seconds < 0) $seconds=0;
	    $TimeInterval = $minutes.' minutes '. $seconds.' seconds ';
	    return $TimeInterval;
	}
	function getSessionLoginMessage()
	{
		$msg = "<div align='center' style='font-size:20px;font-style: italic;'>";
		$msg .= "<p>You cannot sign in as your session is already logged in</p>";
		$msg .= "<p>We'll redirect you in a moment......</p>";
		$msg .= "<p>SECURITY TOKEN :: ".decodeData($_SESSION['iUserId'])."</p>";
		$msg .= "</div>";

		echo $msg;
	}
	function checkEmptyStatus($fieldsArr){
		
		foreach ($fieldsArr as $value) {
			if(empty($value) || $value == "00:00:00"){
				$status = FALSE;
				break;
			}else{
				$status = TRUE;
			}
		}
		return $status;
	}
	/*=======================================SESSION=======================================*/
	function is_userlogged_in(){
		//for User
    	//$is_logged_in = $_SESSION['iUserId'];
    	if( isset($_SESSION['iUserId'])){
    		return TRUE;
    	}
    }
    function is_agentlogged_in(){
    	//for Agent
	  	//$is_logged_in = $_SESSION['iAgentId'];
    	if( isset($_SESSION['iAgentId'])){
    		return TRUE;
    	}
    }
    
	function is_adminlogged_in($loginPage = ''){
		//for admin
	  	$is_logged_in = $_SESSION['iAdminUserId'];
	  	if( !isset($is_logged_in)){
			return redirect(admin_url('login'));
		}
		
	}
	function is_adminsignin_loggedin($loginPage = ''){
		//for admin Login Page
	  	$is_logged_in = @$_SESSION['iAdminUserId'];
	  	if( isset($is_logged_in)){
			return redirect(admin_url('dashboard'));
		}
	}
	/*=======================================SECURITY=======================================*/
	function generateStoreId($MobileNo)
	{
		$last3Digit = substr($MobileNo, -3);
		return $last3Digit . date('His');
	}
	function encryptPassword($password){
		$options = [
		    'APP_SECRET_KEY' => APP_SECRET_KEY,
		];
		$hash = password_hash($password, PASSWORD_BCRYPT, $options);
		return $hash;
	}
	function verifyPassword($password,$hash){
		if (MD5($password) == $hash) {
		    return TRUE;
		}
	}
	function encodeData($data){
		return base64_encode($data);
	}
	function decodeData($data){
		return base64_decode($data);
	}
?>
