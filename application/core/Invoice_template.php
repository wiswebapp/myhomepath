<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_template extends CI_Controller {

	public function CommonEmailHeader(){
		$mh = '';

		$mh .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
		$mh .= '   <tr>';
		$mh .= '      <td style="padding: 10px 0 30px 0;">';
		$mh .= '         <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #cccccc; border-collapse: collapse;">';
		$mh .= '            <tr>';
		$mh .= '               <td align="center" bgcolor="#000" style="padding: 20px 0 30px 0; color: #000; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">';
		$mh .= '                  <img src="https://res.cloudinary.com/urbanclap/image/upload/v1490682269/web-assets/Logo-new.png" alt="Logo" width="220" height="50" style="display: block;" />';
		$mh .= '               </td>';
		$mh .= '            </tr>';

    	return $mh;
	}
	public function CommonEmailFooter(){

		$SITE_NAME = $this->__getConfiguration('SITE_NAME');

		$mf .= '			<tr>';
		$mf .= '               <td bgcolor="#000" style="padding: 30px 30px 30px 30px;">';
		$mf .= '                  <table border="0" cellpadding="0" cellspacing="0" width="100%">';
		$mf .= '                     <tr align="center">';
		$mf .= '                        <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;" width="100%">';
		$mf .= '                           &reg; '.date('Y').' | '.$SITE_NAME.'<br/>';
		$mf .= '                        </td>';
		$mf .= '                     </tr>';
		$mf .= '                  </table>';
		$mf .= '               </td>';
		$mf .= '            </tr>';
		$mf .= '         </table>';
		$mf .= '      </td>';
		$mf .= '   </tr>';
		$mf .= '</table>';

		return $mf;
	}
	public function getCompanyEmail($emailData){
		$SITE_NAME = $this->__getConfiguration('SITE_NAME');
		
		$vOrgName = $emailData['vOrgName'];
		$vUserEmail = $emailData['vUserEmail'];
		$iOrgId = $emailData['iOrgId'];

		$emailBody .= '<tr>';
		$emailBody .= '   <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">';
		$emailBody .= '      <table border="0" cellpadding="0" cellspacing="0" width="100%">';
		$emailBody .= '         <tr>';
		$emailBody .= '            <td style="color: #000; font-family: Arial, sans-serif; font-size: 24px;">';
		$emailBody .= '                <b>Hello '.$vOrgName.'</b>';
		$emailBody .= '            </td>';
		$emailBody .= '         </tr>';
		$emailBody .= '         <tr>';
		$emailBody .= '            <td style="padding: 20px 0 30px 0; color: #000; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">';
		$emailBody .= '               Welcome to '.$SITE_NAME.'.we are happy that you have registered with us.you have registered with '.$vUserEmail.'.You can login into partner application using following store id.';
		$emailBody .= '               <!-- DETAILS SECTION STARTED HERE -->';
		$emailBody .= '               <table style="margin-top: 10px;" cellpadding="5">';
		$emailBody .= '                   <tr><td><strong>Your Store Name : </strong></td><td> '.$vOrgName.' </td></tr>';
		$emailBody .= '                   <tr><td><strong>Your Store Id : </strong></td><td> '.$iOrgId.' </td></tr>';
		$emailBody .= '               </table>';
		$emailBody .= '            </td>';
		$emailBody .= '         </tr>';
		$emailBody .= '        <tr><td style="font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">Looking Forward to provide service to you.</td></tr>';
		$emailBody .= '      </table>';
		$emailBody .= '   </td>';
		$emailBody .= '</tr>';

		return $emailBody;
	}
}