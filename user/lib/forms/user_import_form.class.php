<?php
/**
 * $Id: user_import_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);

class UserImportForm extends FormValidator
{
    
    const TYPE_IMPORT = 1;
    
    private $failedcsv;
    private $current_tag;
    private $current_value;
    private $user;
    private $form_user;
    private $users;
    private $udm;

    /**
     * Creates a new UserImportForm 
     * Used to import users from a file
     */
    function UserImportForm($form_type, $action, $form_user)
    {
        parent :: __construct('user_import', 'post', $action);
        
        $this->form_user = $form_user;
        $this->form_type = $form_type;
        $this->failedcsv = array();
        if ($this->form_type == self :: TYPE_IMPORT)
        {
            $this->build_importing_form();
        }
    }

    function build_importing_form()
    {
        $this->addElement('file', 'file', Translation :: get('FileName'));
        $allowed_upload_types = array('xml', 'csv');
        $this->addRule('file', Translation :: get('OnlyXMLCSVAllowed'), 'filetype', $allowed_upload_types);
        
        $group = array();
        $group[] = & $this->createElement('radio', 'send_mail', null, Translation :: get('Yes'), 1);
        $group[] = & $this->createElement('radio', 'send_mail', null, Translation :: get('No'), 0);
        $this->addGroup($group, 'mail', Translation :: get('SendMailToNewUser'), '&nbsp;');
        
        //$this->addElement('submit', 'user_import', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults['mail']['send_mail'] = 1;
        $this->setDefaults($defaults);
    }

    function import_users()
    {
        $values = $this->exportValues();
        
        $csvusers = $this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
        $validusers = array();
        
        $failures = 0;
        //dump($csvusers);
        foreach ($csvusers as $csvuser)
        {
        	$validuser = $this->validate_data($csvuser);
             
        	if (!$validuser)
            {
            	$failures ++;
                $this->failedcsv[] = Translation :: get('Invalid') . ': ' . implode($csvuser, ';');
            }
            else 
            {
            	$validusers[] = $validuser;
            }
        }
        
    	if ($failures > 0)
        {
            return false;
        }
        
        $udm = UserDataManager :: get_instance();
        
        foreach($validusers as $csvuser)
        {
        	$action = strtoupper($csvuser['action']);

        	if($action == 'A')
        	{
	        	$user = new User();
	                
	            $user->set_firstname($csvuser[User :: PROPERTY_FIRSTNAME]);
	            $user->set_lastname($csvuser[User :: PROPERTY_LASTNAME]);
	            $user->set_username($csvuser[User :: PROPERTY_USERNAME]);
	            
	            $pass = $csvuser[User :: PROPERTY_PASSWORD];
	            if (! $pass || $pass == "")
	                $pass = uniqid();
	            $pass = Hashing :: hash($pass);
	            
	            $user->set_password($pass);
	            $user->set_email($csvuser[User :: PROPERTY_EMAIL]);
	            $user->set_language($csvuser[User :: PROPERTY_LANGUAGE]);
	            $user->set_status($csvuser[User :: PROPERTY_STATUS]);
	            $user->set_active($csvuser[User :: PROPERTY_ACTIVE]);
	            $user->set_official_code($csvuser[User :: PROPERTY_OFFICIAL_CODE]);
	            $user->set_phone($csvuser[User :: PROPERTY_PHONE]);
	            $user->set_auth_source($csvuser[User :: PROPERTY_AUTH_SOURCE]);
	            
	            $act_date = $csvuser[User :: PROPERTY_ACTIVATION_DATE];
	            if ($act_date != 0)
	                $act_date = Utilities :: time_from_datepicker($act_date);
	            
	            $user->set_activation_date($act_date);
	            
	            $exp_date = $csvuser[User :: PROPERTY_EXPIRATION_DATE];
	            if ($exp_date != 0)
	                $exp_date = Utilities :: time_from_datepicker($exp_date);
	            
	            $user->set_expiration_date($exp_date);
	            
	            $user->set_platformadmin(0);
	            if (! $user->create())
	            {
	                $failures ++;
	                $this->failedcsv[] = Translation :: get('CreateFailed') . ': ' . implode($csvuser, ';');
	            }
	            else
	            {
	                $send_mail = intval($values['mail']['send_mail']);
	                if ($send_mail)
	                {
	                    $this->send_email($user);
	                }
	                
	                Events :: trigger_event('import', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->form_user->get_id()));
	            }
        	}
        	elseif($action == 'U')
        	{
        		$user = $udm->retrieve_user_by_username($csvuser[User :: PROPERTY_USERNAME]);
        		$user->set_firstname($csvuser[User :: PROPERTY_FIRSTNAME]);
	            $user->set_lastname($csvuser[User :: PROPERTY_LASTNAME]);
	            
	            $user->set_email($csvuser[User :: PROPERTY_EMAIL]);
	            $user->set_language($csvuser[User :: PROPERTY_LANGUAGE]);
	            $user->set_status($csvuser[User :: PROPERTY_STATUS]);
	            $user->set_active($csvuser[User :: PROPERTY_ACTIVE]);
	            $user->set_official_code($csvuser[User :: PROPERTY_OFFICIAL_CODE]);
	            $user->set_phone($csvuser[User :: PROPERTY_PHONE]);
	            $user->set_auth_source($csvuser[User :: PROPERTY_AUTH_SOURCE]);
	            
	            $act_date = $csvuser[User :: PROPERTY_ACTIVATION_DATE];
	            if ($act_date != 0)
	                $act_date = Utilities :: time_from_datepicker($act_date);
	            
	            $user->set_activation_date($act_date);
	            
	            $exp_date = $csvuser[User :: PROPERTY_EXPIRATION_DATE];
	            if ($exp_date != 0)
	                $exp_date = Utilities :: time_from_datepicker($exp_date);
	            
	            $user->set_expiration_date($exp_date);
	            
	            $pass = $csvuser[User :: PROPERTY_PASSWORD];
	            if ($pass)
	            {
	            	$pass = Hashing :: hash($pass);
	            	$user->set_password($pass);
	            }
	            
        	 	if (!$user->update())
	            {
	                $failures ++;
	                $this->failedcsv[] = Translation :: get('UpdateFailed') . ': ' . implode($csvuser, ';');
	            }
        	}
        	elseif($action == 'D')
        	{
        		$user = $udm->retrieve_user_by_username($csvuser[User :: PROPERTY_USERNAME]);
	        	if (!$user->delete())
	            {
	                $failures ++;
	                $this->failedcsv[] = Translation :: get('DeleteFailed') . ': ' . implode($csvuser, ';');
	            }
        	}
        }
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        } 
    }

    function get_failed_csv()
    {
        return implode($this->failedcsv, '<br />');
    }

    function validate_data($csvuser)
    {
        $failures = 0;
        $udm = UserDataManager :: get_instance();
        
        if ($csvuser['user_name'])
            $csvuser[User :: PROPERTY_USERNAME] = $csvuser['user_name'];
            
        //1. Action valid ?
    	$action = strtoupper($csvuser['action']);
        if($action != 'A' && $action != 'D' && $action != 'U')
        {
        	$failures++; 
        }

        //1. Check if username exists
        if ( ($action == 'A' && !$udm->is_username_available($csvuser[User :: PROPERTY_USERNAME]))  || 
        	 ($action != 'A' && $udm->is_username_available($csvuser[User :: PROPERTY_USERNAME])  ))
		{
            $failures ++;
        }
        
        //2. Check status
        if ($csvuser[User :: PROPERTY_STATUS])
        {
            if ($csvuser[User :: PROPERTY_STATUS] != 5 && $csvuser[User :: PROPERTY_STATUS] != 1)
            {
                $failures ++;
            }
        }
        else
        {
            $csvuser[User :: PROPERTY_STATUS] = 5;
        }
        
        $email = $csvuser[User :: PROPERTY_EMAIL];
        
        if ($csvuser['phone_number'])
            $csvuser[User :: PROPERTY_PHONE] = $csvuser['phone_number'];
        
        if (!$csvuser[User :: PROPERTY_ACTIVE])
            $csvuser[User :: PROPERTY_ACTIVE] = 1;
        
        if (! $csvuser[User :: PROPERTY_ACTIVATION_DATE])
            $csvuser[User :: PROPERTY_ACTIVATION_DATE] = 0;
        
        if (! $csvuser[User :: PROPERTY_EXPIRATION_DATE])
            $csvuser[User :: PROPERTY_EXPIRATION_DATE] = 0;
        
        if (! $csvuser[User :: PROPERTY_AUTH_SOURCE])
            $csvuser[User :: PROPERTY_AUTH_SOURCE] = 'platform';
        
        if (! $csvuser[User :: PROPERTY_LANGUAGE])
            $csvuser[User :: PROPERTY_LANGUAGE] = 'english';
        
        if (PlatformSetting :: get('require_email', UserManager :: APPLICATION_NAME) && (! $email || $email == ''))
        {
            $failures ++;
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return $csvuser;
        }
    }

    function parse_file($file_name, $file_type)
    {
        
        $this->users = array();
        if ($file_type == 'text/csv' || $file_type == 'application/vnd.ms-excel' || $file_type == 'application/octet-stream' || $file_type == 'application/force-download')
        {
            $this->users = Import :: csv_to_array($file_name);
        }
        elseif ($file_type == 'text/xml')
        {
            $parser = xml_parser_create();
            xml_set_element_handler($parser, array(get_class(), 'element_start'), array(get_class(), 'element_end'));
            xml_set_character_data_handler($parser, array(get_class(), 'character_data'));
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
            xml_parse($parser, file_get_contents($file_name));
            xml_parser_free($parser);
        }
        return $this->users;
    }

    function element_start($parser, $data)
    {
        switch ($data)
        {
            case 'Contact' :
                $this->user = array();
                break;
            default :
                $this->current_tag = $data;
        }
    }

    /**
     * XML-parser: handle end of element
     */
    function element_end($parser, $data)
    {
        switch ($data)
        {
            case 'Contact' :
                if ($this->user['Status'] == '5')
                {
                    $this->user['Status'] = 5;
                }
                if ($this->user['Status'] == '1')
                {
                    $this->user['Status'] = 1;
                }
                $this->users[] = $this->user;
                break;
            default :
                $this->user[$data] = $this->current_value;
                break;
        }
    }

    /**
     * XML-parser: handle character data
     */
    function character_data($parser, $data)
    {
        $this->current_value = $data;
    }

    function send_email($user)
    {
        global $rootWeb;
        $firstname = $user->get_firstname();
        $lastname = $user->get_lastname();
        $username = $user->get_username();
        $password = $this->unencryptedpass;
        
        $subject = '[' . PlatformSetting :: get('site_name') . '] ' . Translation :: get('YourReg') . ' ' . PlatformSetting :: get('site_name');
        $body = Translation :: get('Dear') . " " . stripslashes("$firstname $lastname") . ",\n\n" . Translation :: get('YouAreReg') . " " . PlatformSetting :: get('site_name') . " " . Translation :: get('Settings') . " " . $username . "\n" . Translation :: get('Password') . " : " . stripslashes($password) . "\n\n" . Translation :: get('Address') . " " . PlatformSetting :: get('site_name') . " " . Translation :: get('Is') . " : " . $rootWeb . "\n\n" . Translation :: get('Problem') . "\n\n" . Translation :: get('Formula') . ",\n\n" . PlatformSetting :: get('administrator_firstname') . " " . PlatformSetting :: get('administrator_surname') . "\n" . Translation :: get('Manager') . " " . PlatformSetting :: get('site_name') . "\nT. " . PlatformSetting :: get('administrator_telephone') . "\n" . Translation :: get('Email') . " : " . PlatformSetting :: get('administrator_email');
        
        $mail = Mail :: factory($subject, $body, $user->get_email(), PlatformSetting :: get('administrator_email'));
        $mail->send();
    }
}
?>