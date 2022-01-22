<?php 

/*
required
matches                 matches[form_item]
regex_match             regex_match[/regex/]
differs                 differs[form_item]
is_unique               is_unique[table.field]
min_length              min_length[3]
max_length              max_length[12]
exact_length            exact_length[8]
greater_than            greater_than[8]
greater_than_equal_to   greater_than_equal_to[8]
less_than               less_than[8]
less_than_equal_to      less_than_equal_to[8]
in_list                 in_list[red,blue,green]
alpha
alpha_numeric_spaces
alpha_dash              form contains anything other than alpha-numeric characters, underscores or dashes.  
numeric
integer
decimal
valid_url       No      Returns FALSE if the form element does not contain a valid URL.  
valid_email     No      Returns FALSE if the form element does not contain a valid email address.        
valid_emails
valid_ip
*/
$config = array(
        'checkUserLogin' => array(
                array('field' => 'username','label' => 'Username','rules' => 'required'),
                array('field' => 'password','label' => 'Password','rules' => 'required|min_length[5]'),
        ),
        'checkForgetData' => array(
                array('field' => 'vEmail','label' => 'Email Address','rules' => 'required|valid_email'),
        ),
        'checkMobileVerification' => array(
                array('field' => 'vMobile',
                        'label' => 'Mobile Number',
                        'rules' => 'required|numeric|exact_length[10]|min_length[10]|max_length[10]'
                ),
                array('field' => 'vRefCode','label' => 'Referral Code','rules' => 'exact_length[10]'),
        ),
        'checkUserSignup' => array(
                array('field' => 'vName','label' => 'First Name','rules' => 'required|alpha_numeric_spaces'),
                array('field' => 'vEmail','label' => 'Email Address','rules' => 'required|valid_email'),
                array('field' => 'vPassword','label' => 'Password','rules' => 'required|min_length[5]'),
                array('field' => 'vPhone',
                        'label' => 'Mobile Number',
                        'rules' => 'required|numeric|exact_length[10]|min_length[10]|max_length[10]'
                ),
        ),
        /*===================================ADMIN PANEL RULES===================================*/
        'checkAdminLogin' => array(
                array('field' => 'username','label' => 'Username','rules' => 'required'),
                array('field' => 'password','label' => 'Password','rules' => 'required|min_length[5]'),
        ),
        'checkRegUser' => array(
                array('field' => 'vName','label' => 'First Name','rules' => 'required|alpha_numeric_spaces'),
                array('field' => 'vEmail','label' => 'Email Address','rules' => 'required|valid_email'),
                array('field' => 'vPassword','label' => 'Password','rules' => 'min_length[5]'),
                /*array('field' => 'vCountry','label' => 'Country','rules' => 'required'),*/
                array('field' => 'vPhone',
                        'label' => 'Mobile Number',
                        'rules' => 'required|numeric|exact_length[10]|min_length[10]|max_length[10]'
                ),
        ),
        
        'checkOrganization' => array(
                array('field' => 'vUserEmail','label' => 'Email Address','rules' => 'required|valid_email'),
                array('field' => 'vPassword','label' => 'Password','rules' => 'min_length[5]'),
                array('field' => 'vUserName','label' => 'User Name','rules' => 'required|alpha_numeric_spaces'),
                array('field' => 'vAddress','label' => 'Address','rules' => 'required'),
                array('field' => 'vZipCode','label' => 'Zip Code','rules' => 'required|numeric|exact_length[6]'),
                array('field' => 'vCountry','label' => 'Country','rules' => 'required'),
                array('field' => 'vState','label' => 'State','rules' => 'required'),
                array('field' => 'vCity','label' => 'City','rules' => 'required'),
                array('field' => 'vUserMobile',
                        'label' => 'Mobile Number',
                        'rules' => 'required|numeric|exact_length[10]|min_length[10]|max_length[10]'
                ),
        ),
);

?>