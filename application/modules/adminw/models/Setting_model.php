<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends MY_Model {

	public function __construct(){
        parent::__construct();
    }

    public function getSettingData(){
    	//also in settings.php file
    	$tabArray = array('General','Email','Payment','Social Media','App Settings');

    	foreach ($tabArray as $tab) {
    		$where = array('eAdminDisplay'=>'Yes','eStatus' =>'Active','eType' =>"$tab");
    		$op[cleantab($tab)] = $this->db->select('*')->from('configurations')->where($where)->get()->result_array();
    	}
    	return $op;
    }
    public function updateSettings($tabname,$postdata){
        is_adminlogged_in();
        foreach ($postdata as $key => $update) {

            $this->db->set('vValue', $update)
                    ->where('vName', $key)
                    ->update('configurations');
        }
        return TRUE;
    }

    public function getGeneralData(){
        
        $returnArr = $this->db->select('vName,vValue')
                                ->from('configurations')
                                ->where('eStatus','Active')
                                ->get();
        return $returnArr->result_array();

    }
}

?>