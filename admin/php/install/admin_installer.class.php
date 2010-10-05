<?php
/**
 * $Id: admin_installer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.install
 */
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class AdminInstaller extends Installer
{

    /**
     * Constructor
     */
    function AdminInstaller($values)
    {
        parent :: __construct($values, AdminDataManager :: get_instance());
    }

    /**
     * Runs the install-script.
     */
    function install_extra()
    {
        
        // Add the default language entries in the database
        if (! $this->create_languages())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('LanguagesAdded'));
        }
        
        // Update the default settings to the database
        if (! $this->update_settings())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('DefaultSettingsAdded'));
        }
        
        return true;
    }

    function create_languages()
    {	
    	$root = dirname(__FILE__) . '/../../../languages/';
    	$folders = Filesystem :: get_directory_content($root, Filesystem :: LIST_DIRECTORIES, false);
    	
    	foreach($folders as $folder)
    	{
    		//if(Text :: char_at($folder, 0) != '.')
    		if(file_exists($root . $folder . '/language.xml'))
    		{
    			$language = new Language();
    			$xml_data = Utilities :: extract_xml_file($root . $folder . '/language.xml');

    			$language->set_original_name($xml_data['original']);
    			$language->set_english_name($xml_data['english']);
    			$language->set_folder($xml_data['folder']);
    			$language->set_isocode($xml_data['isocode']);
    			$language->set_available('1');
    			
	    		if ($language->create())
		        {
		            $this->add_message(self :: TYPE_NORMAL, Translation :: get('LanguageAdded') . ' ' . $xml_data['english']);
		        }
		        else 
		        {
		        	return false;
		        }
    		}
    	}
    	
        return true;
    }

    function update_settings()
    {
        $values = $this->get_form_values();
        
        $settings = array();
        $settings[] = array('admin', 'site_name', $values['platform_name']);
        $settings[] = array('admin', 'server_type', 'production');
        $settings[] = array('admin', 'platform_language', $values['platform_language']);
        $settings[] = array('admin', 'version', '2.0');
        $settings[] = array('admin', 'theme', 'aqua');
        
        $settings[] = array('admin', 'institution', $values['organization_name']);
        $settings[] = array('admin', 'institution_url', $values['organization_url']);
        
        $settings[] = array('admin', 'show_administrator_data', 'true');
        $settings[] = array('admin', 'administrator_firstname', $values['admin_firstname']);
        $settings[] = array('admin', 'administrator_surname', $values['admin_surname']);
        $settings[] = array('admin', 'administrator_email', $values['admin_email']);
        $settings[] = array('admin', 'administrator_telephone', $values['admin_phone']);
        
        //$settings[] = array('user', 'allow_password_retrieval', $values['encrypt_password']);
        $settings[] = array('user', 'allow_registration', $values['self_reg']);
        
        foreach ($settings as $setting)
        {
            $setting_object = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name($setting[1], $setting[0]);
            $setting_object->set_application($setting[0]);
            $setting_object->set_variable($setting[1]);
            $setting_object->set_value($setting[2]);
            
            if (! $setting_object->update())
            {
                print_r($setting);
                return false;
            }
        }
        
        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>