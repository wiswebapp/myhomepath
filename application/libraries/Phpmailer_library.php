<?php 
(! defined('BASEPATH')) and exit('No direct script access allowed');

$googleLibFilePath =  APPPATH."/third_party/phpmailer/";
require ($googleLibFilePath . 'src/Exception.php');
require ($googleLibFilePath . 'src/PHPMailer.php');
require ($googleLibFilePath . 'src/SMTP.php');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Phpmailer_library
{
    public function __construct()
    {
        log_message('Debug', 'PHPMailer class is loaded.');
    }

    public function __loadsmtp()
    {
        
        $objMail = new PHPMailer;
        return $objMail;
    }
}
?>