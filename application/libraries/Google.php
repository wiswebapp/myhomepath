<?php 
$googleLibFile =  APPPATH."/third_party/googlelogin/autoload.php";
require_once($googleLibFile);

class Google {
	protected $CI;
	public function __construct(){
		$this->CI =& get_instance();
		$GOOGLE_OAUTH_CLIENT_ID = $this->CI->__getConfiguration('GOOGLE_OAUTH_CLIENT_ID');
		$GOOGLE_OAUTH_SECRET_KEY = $this->CI->__getConfiguration('GOOGLE_OAUTH_SECRET_KEY');
		$GOOGLE_OAUTH_REDIRECT_URL = $this->CI->__getConfiguration('GOOGLE_OAUTH_REDIRECT_URL');

        $this->client = new Google_Client();
		$this->client->setClientId($GOOGLE_OAUTH_CLIENT_ID);
		$this->client->setClientSecret($GOOGLE_OAUTH_SECRET_KEY);
		$this->client->setRedirectUri($GOOGLE_OAUTH_REDIRECT_URL);
		$this->client->setScopes(array(
			"https://www.googleapis.com/auth/plus.login",
			"https://www.googleapis.com/auth/plus.me",
			"https://www.googleapis.com/auth/userinfo.email",
			"https://www.googleapis.com/auth/userinfo.profile"
			)
		);
  
	}
	public function get_login_url(){
		return  $this->client->createAuthUrl();
	}
	public function validate(){		
		if (isset($_GET['code'])) {
		  $this->client->authenticate($_GET['code']);
		  $_SESSION['access_token'] = $this->client->getAccessToken();
		}
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
		  $this->client->setAccessToken($_SESSION['access_token']);
		  $plus = new Google_Service_Plus($this->client);
			$person = $plus->people->get('me');
			$info['id']=$person['id'];
			$info['email']=$person['emails'][0]['value'];
			$info['name']=$person['displayName'];
			$info['link']=$person['url'];
			$info['profile_pic']=substr($person['image']['url'],0,strpos($person['image']['url'],"?sz=50")) . '?sz=800';
		   return  $info;
		}
	}
}