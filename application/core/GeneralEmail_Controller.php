<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once ('Invoice_template.php');

class GeneralEmail_Controller extends Invoice_template {

	public function __construct(){
		//loading  library
		parent::__construct();
		$this->load->library('email');
		$this->load->library("phpmailer_library");
	}

	public function sendEmailFromSystem($FROM_EMAIL, $FROM_NAME = '', $TO_EMAIL, $SUBJECT, $MESSAGE ){
		
		$ENABLE_SMTP_OR_MAILGUN = $this->__getConfiguration('ENABLE_SMTP_OR_MAILGUN');
		$NOREPLY_EMAIL = $this->__getConfiguration('NOREPLY_EMAIL');
		$SITE_NAME = $this->__getConfiguration('SITE_NAME');
		$FROM_NAME = empty($FROM_NAME) ? SITE_NAME : $FROM_NAME;
		/*
			If SMTP Enable Than Send Through SMTP If not Than Default CI Email 
			if mail is still not send than using tradition mail() function
		*/
		if($ENABLE_SMTP_OR_MAILGUN == "Yes"){
			$objMail = $this->phpmailer_library->__loadsmtp();
			$SMTPHOST = $this->__getConfiguration('SMTP_HOST_FOR_EMAIL');
	        $SMTPUSERNAME = $this->__getConfiguration('SMTP_USERNAME_FOR_EMAIL');
	        $SMTPPASSWORD = $this->__getConfiguration('SMTP_PASSWORD_FOR_EMAIL');
	        $objMail->isSMTP();
            $objMail->Host       = $SMTPHOST;
            $objMail->SMTPAuth   = true;
            $objMail->Username   = $SMTPUSERNAME;
            $objMail->Password   = $SMTPPASSWORD;
            $objMail->Port       = 587;
            //Recipients
            $objMail->setFrom($NOREPLY_EMAIL, APP_TITLE);
            $objMail->addAddress($TO_EMAIL);
            // Content
            $objMail->isHTML(true);
            $objMail->Subject = $SUBJECT;
            $objMail->Body    = $MESSAGE;

            //$mailsend = $objMail->send();
		}else{
			$this->email->set_mailtype("html");
			$this->email->from($FROM_EMAIL, $FROM_NAME);
			$this->email->to($TO_EMAIL);
			$this->email->subject($SUBJECT);
			$this->email->message($MESSAGE);
			//$mailsend = $this->email->send();
		}

		if(!$mailsend){
			$headers = '';
	        $headers = "MIME-Version: 1.0" . "\r\n";
	        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	        $headers .= "From: ".$SITE_NAME." < $NOREPLY_EMAIL > \n";

	        //$mailsend = mail ($TO_EMAIL,$SUBJECT,$MESSAGE,$headers);
		}

		return $mailsend;
	}

	public function getEmailTemplate($emailCode, $emaildata = array()){
		
		if( !empty($emailCode) ){

			$message = "";
			$SITE_NAME = $this->__getConfiguration('SITE_NAME');

			switch ($emailCode) {
				case 'TEMPLATE_ORGANIZATION_REGISTER':
					$data['subject'] = "Store registration successful on ". $SITE_NAME;
					$message .= $this->CommonEmailHeader();
					$message .= $this->getCompanyEmail($emaildata);
					$message .= $this->CommonEmailFooter();
					$data['message'] = $message;
					break;
				default:
					$subject = "EMAIL ERROR";
					$message = "EMAIL ERROR";
					break;
			}

			return $data;
		}
	}
}
