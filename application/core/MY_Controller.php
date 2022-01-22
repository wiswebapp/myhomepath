<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends General_Controller {
	
	public function __construct(){
		parent::__construct();
		//for captcha
		$this->load->helper('captcha');
	}

	protected function __setflash( $flag, $onsuccess = 'Success', $onerror = 'Error', $redirect='' ){
		$icon = '<i class="fa fa-exclamation-triangle"></i> ';

		$onsuccess = $icon . $onsuccess;
		$onerror = $icon . $onerror;
		
		if($flag === "INFO"){
			$this->session->set_flashdata('feedback',$onsuccess);
			$this->session->set_flashdata('feedback_class', 'alert-info');
		}elseif( $flag > 0 && $flag == TRUE) {
			$this->session->set_flashdata('feedback',$onsuccess);
			$this->session->set_flashdata('feedback_class', 'alert-success');
		}else {
			$this->session->set_flashdata('feedback', $onerror);
			$this->session->set_flashdata('feedback_class', 'alert-danger');
		}
		
		if(!empty($redirect)){
			redirect( $redirect);
		}else{
			return true;
		}
	}

	public function _solo_file_upload($filename, $pathToUpload, $config = '',$generatedFileName=''){

		$config = array(
			'upload_path' => $pathToUpload,
			'allowed_types' => "gif|jpg|png|jpeg",
			'max_size' => "4048000", // Can be set to 4 MB(4048 Kb)
		);
		
		if(!empty($generatedFileName)){
			$config['file_name'] = $generatedFileName;
		}
		
		$this->load->library('upload');
		$this->upload->initialize($config);

		if($this->upload->do_upload($filename)){
			$successdata = array('upload_data' => $this->upload->data());
			$data['success'] = 1;
			$data['message'] = $successdata;
		}else{
			$data['success'] = 0;
			$data['message'] = $this->upload->display_errors();
		}
		return $data;
	}

	protected function configPagination($pageurl,$totalrows,$limit,$uri_segment){

		
		$config = array(
				'base_url'	=>	$pageurl,
				'total_rows'=>	$totalrows,
				'per_page'	=>	$limit,
				'uri_segment' => $uri_segment,
				'reuse_query_string' => TRUE,
				//'anchor_class'	=> 'class=\'page-link\' ',
				'attributes'	=> array('class' => 'page-link'),

				'full_tag_open' => "<ul style='float:left' class='pagination'>",
				'full_tag_close' =>"</ul>",

				'num_tag_open' => '<li clas="page-item">',
				'num_tag_close' => '</li>',

				'cur_tag_open' => "<li class='page-item active'><a class='page-link' href='#'>",
				'cur_tag_close' => "</a></li>",

				'next_tag_open' => "<li class='page-item'>",
				'next_tagl_close' => "</li>",

				'prev_tag_open' => "<li class='page-item'>",
				'prev_tagl_close' => "</li>",

				'first_tag_open' => "<li>",
				'first_tagl_close' => "</li>",

				'last_tag_open' => "<li>",
				'last_tagl_close' => "</li>",
		);
		
		return $config;
	}
	
	protected function resize_image($imagepath,$thumbpath){

		$config['image_library'] = 'gd2';
		$config['source_image'] = $imagepath;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width']        = 370;
		$config['height']       = 250;
		$config['new_image']	= $thumbpath;

		$this->load->library('image_lib', $config);
		$this->image_lib->resize();

		if ( ! $this->image_lib->resize()){
			return $this->image_lib->display_errors();
		}else{
			return true;
		}
	}
	/*==============================Captcha Code==============================*/
	protected function loadNewCaptcha(){
        $CaptchaConfig = array(
            'img_path'      => './assets/images/captcha/',
            'img_url'       => assets('images/captcha/'),
            'expiration'    => 7200,
            'img_width'     => 180,
            'img_height'    => 50,
            'word_length'   => 5,
            'pool'          => '0123456789abcdefghijklmnopqrstuvwxyz',
            'font_size'     => 10,
            'font_path' => FCPATH . 'assets/fonts/Muli-Bold.ttf',
            'colors'        => array(
                'background' => array(216, 79, 87),
                'border' => array(255, 255, 255),
                'text' => array(247, 247, 247),
                'grid' => array(255, 40, 40)
        	)
        );

        $captchaData = create_captcha($CaptchaConfig);
	    
        // Unset previous captcha and set new captcha word
	    $this->session->unset_userdata('captchaCode');
	    $this->session->set_userdata('captchaCode',$captchaData['word']);

        return $captchaData;
    }
	protected function captchaVerify($captchaCode){

		$originalCaptcha = $this->session->userdata('captchaCode');
		
		if($captchaCode == $originalCaptcha){
			return TRUE;
		}else{
			return FALSE;
		}
		exit;
	}
	public function generateLog($filepath,$message){

		$current = file_get_contents($rootFile);
        $current .= $data."\n";
        file_put_contents($rootFile, $current);

	}
	public function setWaterMark($imageUrl)
	{
		$this->load->library('image_lib');

		$config['source_image'] = FCPATH.'webimages/tours/1/4.jpg'; //NEED FCPATH
		$config['wm_text'] =  'WEBBOOKING';
		$config['wm_type'] = 'text';
		$config['wm_font_path'] = FCPATH . 'assets/fonts/Muli-Bold.ttf';
		$config['wm_font_size'] = '20';
		$config['wm_font_color'] = 'ffffff';
		$config['wm_vrt_alignment'] = 'bottom';
		$config['wm_hor_alignment'] = 'right';
		$config['wm_padding'] = 0;
		$config['wm_opacity'] = 10;
		
		$this->image_lib->initialize($config);

		$op = $this->image_lib->watermark();
		if($op){
			printr($config);
		}
	}
}
