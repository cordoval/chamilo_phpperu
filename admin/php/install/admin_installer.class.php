<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Installer;
use common\libraries\Filesystem;
use common\libraries\Path;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\video_conferencing_manager\VideoConferencingManager;
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
    function __construct($values)
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
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectsAdded', array('OBJECTS' => Translation :: get('Languages')), Utilities :: COMMON_LIBRARIES));
        }
        
        // Update the default settings to the database
        if (! $this->update_settings())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectsAdded', array('OBJECTS' => Translation :: get('DefaultSettings')), Utilities :: COMMON_LIBRARIES));
        }
        
        // Register the common libraries
        if (! $this->register_common_libraries())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectsAdded', array('OBJECTS' => Translation :: get('Libraries', null, ExternalRepositoryManager :: get_namespace())), Utilities :: COMMON_LIBRARIES));
        }
        
        // Register the common extensions
        if (! $this->register_common_extensions())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectsAdded', array('OBJECTS' => Translation :: get('Extensions', null, ExternalRepositoryManager :: get_namespace())), Utilities :: COMMON_LIBRARIES));
        }
        
        return true;
    }

    function create_languages()
    {
        $language_path = Path :: get_common_libraries_path() . 'resources/i18n/';
        $language_files = Filesystem :: get_directory_content($language_path, Filesystem :: LIST_FILES, false);
        
        foreach ($language_files as $language_file)
        {
            $file_info = pathinfo($language_file);
            $language_info_file = $language_path . $file_info['filename'] . '.info';
            
            if (file_exists($language_info_file) && $file_info['extension'] == 'info')
            {
                $package_info = PackageInfo :: factory(Registration :: TYPE_LANGUAGE, $file_info['filename'])->get_package_info();
                
                $language = new Language();
                $language->set_original_name($package_info['package']['name']);
                $language->set_english_name($package_info['package']['extra']['english']);
                $language->set_family($package_info['package']['category']);
                $language->set_isocode($package_info['package']['extra']['isocode']);
                $language->set_available('1');
                
                if ($language->create())
                {
                    $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectAdded', array('OBJECT' => Translation :: get('Language')), Utilities :: COMMON_LIBRARIES) . ' ' . $xml_data['english']);
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
                return false;
            }
        }
        
        return true;
    }

    function register_common_extensions()
    {
        $common_extensions_path = Path :: get_common_extensions_path();
        $folders = Filesystem :: get_directory_content($common_extensions_path, Filesystem :: LIST_DIRECTORIES, false);
        
        foreach ($folders as $folder)
        {
            $package_info = PackageInfo :: factory(Registration :: TYPE_EXTENSION, $folder);
            $package_info = $package_info->get_package();
            $registration = new Registration();
            $registration->set_name($folder);
            $registration->set_type(Registration :: TYPE_EXTENSION);
            $registration->set_category($package_info->get_category());
            $registration->set_version($package_info->get_version());
            $registration->set_status(Registration :: STATUS_ACTIVE);
            if (! $registration->create())
            {
                return false;
            }
        }
        
        return true;
    }

    function register_common_libraries()
    {
        $package_info = PackageInfo :: factory(Registration :: TYPE_LIBRARY, null);
        $package_info = $package_info->get_package();
        $registration = new Registration();
        $registration->set_name($package_info->get_code());
        $registration->set_type(Registration :: TYPE_LIBRARY);
        $registration->set_category($package_info->get_category());
        $registration->set_version($package_info->get_version());
        $registration->set_status(Registration :: STATUS_ACTIVE);
        if (! $registration->create())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * @return string
     */
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>