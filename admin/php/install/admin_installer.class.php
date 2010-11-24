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

        // Register the common extensions
        if (! $this->register_common_extensions())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectsAdded', array('OBJECTS' => Translation :: get('Extensions', null, ExternalRepositoryManager :: get_namespace())), Utilities :: COMMON_LIBRARIES));
        }

        // Register the external repository manager implementations
        if (! $this->register_external_repository_managers())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectsAdded', array('OBJECTS' => Translation :: get('ExternalRepositories', null, ExternalRepositoryManager :: get_namespace())), Utilities :: COMMON_LIBRARIES));
        }

        // Register the video conferencing manager implementations
        if (! $this->register_video_conferencing_managers())
        {
            return false;
        }
        else
        {
        	echo('hier');
        	dump(VideoConferencingManager :: get_namespace());
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectsAdded', array('OBJECTS' => Translation :: get('VideosConferencing', null, VideoConferencingManager :: get_namespace())), Utilities :: COMMON_LIBRARIES));
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
                $language = new Language();
                $xml_data = Utilities :: extract_xml_file($language_info_file);

                $language->set_original_name($xml_data['original']);
                $language->set_english_name($xml_data['english']);
                $language->set_isocode($xml_data['isocode']);
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
            $registration = new Registration();
            $registration->set_name($folder);
            $registration->set_type(Registration :: TYPE_EXTENSION);
            $registration->set_version('1.0.0');
            $registration->set_status(Registration :: STATUS_ACTIVE);
            if (! $registration->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    function register_external_repository_managers()
    {
        $external_repository_manager_path = Path :: get_common_extensions_path() . 'external_repository_manager/implementation/';
        $folders = Filesystem :: get_directory_content($external_repository_manager_path, Filesystem :: LIST_DIRECTORIES, false);

        foreach ($folders as $folder)
        {
            $registration = new Registration();
            $registration->set_name($folder);
            $registration->set_type(Registration :: TYPE_EXTERNAL_REPOSITORY_MANAGER);
            $registration->set_version('1.0.0');
            $registration->set_status(Registration :: STATUS_ACTIVE);
            if (! $registration->create())
            {
                return false;
            }
        }

        return true;
    }
    
	/**
     * @return boolean
     */
    function register_video_conferencing_managers()
    {
        $video_conferencing_manager_path = Path :: get_common_extensions_path() . 'video_conferencing_manager/implementation/';
        $folders = Filesystem :: get_directory_content($video_conferencing_manager_path, Filesystem :: LIST_DIRECTORIES, false);

        foreach ($folders as $folder)
        {
            $registration = new Registration();
            $registration->set_name($folder);
            $registration->set_type(Registration :: TYPE_VIDEO_CONFERENCING_MANAGER);
            $registration->set_version('1.0.0');
            $registration->set_status(Registration :: STATUS_ACTIVE);
            if (! $registration->create())
            {
                return false;
            }
        }

        return true;
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