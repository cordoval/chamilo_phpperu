<?php
/**
 * $Id: installer.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package common
 * @todo Some more common install-functions can be added here. Example: A
 * function which returns the list of xml-files from a given directory.
 */

abstract class ContentObjectInstaller
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';
    const INSTALL_SUCCESS = 'success';
    const INSTALL_MESSAGE = 'message';
    
    /**
     * The datamanager which can be used by the installer of the application
     */
    private $data_manager;
    
    /**
     * Message to be displayed upon completion of the installation procedure
     */
    private $message;

    /**
     * Constructor
     */
    function ContentObjectInstaller()
    {
        $this->data_manager = RepositoryDataManager :: get_instance();
        $this->message = array();
    }

    function install()
    {
        if (! $this->register_content_object())
        {
            return false;
        }
        
        $dir = $this->get_path();
        $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);
        
        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                if (! $this->create_storage_unit($file))
                {
                    return false;
                }
            }
        }
        
        if (! $this->configure_content_object())
        {
            return false;
        }
        
        if (method_exists($this, 'install_extra'))
        {
            if (! $this->install_extra())
            {
                return false;
            }
        }
        
        if (! $this->import_content_object())
        {
        	return false;
        }
        
        return $this->installation_successful();
    }
    
	public function import_content_object()
    {
        $type = $this->get_content_object();
    	$file = Path :: get_repository_path() . 'lib/content_object/' . $type . '/install/example.zip';

    	if (file_exists($file))
    	{
	    	$condition = new EqualityCondition(User::PROPERTY_PLATFORMADMIN, 1);
	        $user = UserDataManager::get_instance()->retrieve_users($condition)->next_result();
	        $category = RepositoryDataManager::get_instance();
        
	        
	    	$import = ContentObjectImport::factory('cpo', array('tmp_name' => $file), $user, 0);
	        if (! $import->import_content_object())
	        {
	        	$message = Translation :: get('ContentObjectImportFailed');
                $this->installation_failed($message);
                return false;
	        }
	        else
	        {
	        	$this->add_message(self :: TYPE_NORMAL, Translation :: get('ImportSuccessfull'));
	        }
    	}
    	return true;
    }
    

    function get_content_object()
    {
        $content_object_class = $this->get_content_object_name();
        $content_object = Utilities :: camelcase_to_underscores($content_object_class);
        
        return $content_object;
    }

    function get_content_object_name()
    {
        $content_object_class = str_replace('ContentObjectInstaller', '', get_class($this));
        
        return $content_object_class;
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used as the
     * PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database.
     * mdb2.datatypes.php
     * @param string $file The complete path to the XML-file from which the
     * storage unit definition should be read.
     * @return array An with values for the keys 'name','properties' and
     * 'indexes'
     */
    public static function parse_xml_file($file)
    {
        $name = '';
        $properties = array();
        $indexes = array();
        
        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagname('object')->item(0);
        $name = $object->getAttribute('name');
        $xml_properties = $doc->getElementsByTagname('property');
        $attributes = array('type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed');
        foreach ($xml_properties as $index => $property)
        {
            $property_info = array();
            foreach ($attributes as $index => $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $property_info[$attribute] = $property->getAttribute($attribute);
                }
            }
            $properties[$property->getAttribute('name')] = $property_info;
        }
        $xml_indexes = $doc->getElementsByTagname('index');
        foreach ($xml_indexes as $key => $index)
        {
            $index_info = array();
            $index_info['type'] = $index->getAttribute('type');
            $index_properties = $index->getElementsByTagname('indexproperty');
            foreach ($index_properties as $subkey => $index_property)
            {
                $index_info['fields'][$index_property->getAttribute('name')] = array('length' => $index_property->getAttribute('length'));
            }
            $indexes[$index->getAttribute('name')] = $index_info;
        }
        $result = array();
        $result['name'] = $name;
        $result['properties'] = $properties;
        $result['indexes'] = $indexes;
        
        return $result;
    }

    function add_message($type = self :: TYPE_NORMAL, $message)
    {
        switch ($type)
        {
            case self :: TYPE_NORMAL :
                $this->message[] = $message;
                break;
            case self :: TYPE_CONFIRM :
                $this->message[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
                break;
            case self :: TYPE_WARNING :
                $this->message[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
                break;
            case self :: TYPE_ERROR :
                $this->message[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
                break;
            default :
                $this->message[] = $message;
                break;
        }
    }

    function set_message($message)
    {
        $this->message = $message;
    }

    function get_message()
    {
        return $this->message;
    }

    function get_data_manager()
    {
        return $this->data_manager;
    }

    function retrieve_message()
    {
        return implode('<br />' . "\n", $this->get_message());
    }

    /**
     * Parses an XML file and sends the request to the database manager
     * @param String $path
     */
    function create_storage_unit($path)
    { //print_r($path);
        $storage_unit_info = self :: parse_xml_file($path);
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('StorageUnitCreation') . ': <em>' . $storage_unit_info['name'] . '</em>');
        if (! $this->data_manager->create_storage_unit($storage_unit_info['name'], $storage_unit_info['properties'], $storage_unit_info['indexes']))
        {
            return $this->installation_failed(Translation :: get('StorageUnitCreationFailed') . ': <em>' . $storage_unit_info['name'] . '</em>');
        }
        else
        {
            return true;
        }
    }

    function parse_content_object_settings($file)
    {
        $doc = new DOMDocument();
        
        $doc->load($file);
        $object = $doc->getElementsByTagname('application')->item(0);
        
        // Get events
        $events = $doc->getElementsByTagname('setting');
        $settings = array();
        
        foreach ($events as $index => $event)
        {
            $settings[$event->getAttribute('name')] = array('default' => $event->getAttribute('default'), 'user_setting' => $event->getAttribute('user_setting'));
        }
        
        return $settings;
    }

    function configure_content_object()
    {
        $content_object = $this->get_content_object();
        $base_path = Path :: get_repository_path() . 'lib/content_object/' . $content_object;
        $settings_file = $base_path . '/settings/settings_' . $content_object . '.xml';
        
        if (file_exists($settings_file))
        {
            $xml = $this->parse_content_object_settings($settings_file);
            
            foreach ($xml as $name => $parameters)
            {
                $setting = new Setting();
                $setting->set_application(RepositoryManager :: APPLICATION_NAME);
                
                $setting->set_variable($name);
                $setting->set_value($parameters['default']);
                
                $user_setting = $parameters['user_setting'];
                if ($user_setting)
                    $setting->set_user_setting($user_setting);
                else
                    $setting->set_user_setting(0);
                
                if (! $setting->create())
                {
                    $message = Translation :: get('ContentObjectConfigurationFailed');
                    $this->installation_failed($message);
                }
            }
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('SettingsAdded'));
        }
        
        return true;
    }

    function register_content_object()
    {
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('ContentObjectRegistration'));
        
        $content_object_registration = new Registration();
        $content_object_registration->set_type(Registration :: TYPE_CONTENT_OBJECT);
        $content_object_registration->set_name($this->get_content_object());
        $content_object_registration->set_status(Registration :: STATUS_ACTIVE);
        
        $package_info = PackageInfo :: factory(Registration :: TYPE_CONTENT_OBJECT, $this->get_content_object());
        
        if ($package_info)
        {
            $content_object_registration->set_version($package_info->get_package()->get_version());
        }
        
        if (! $content_object_registration->create())
        {
            return $this->installation_failed(Translation :: get('ContentObjectRegistrationFailed'));
        }
        return true;
    }

    function installation_failed($error_message)
    {
        $this->add_message(self :: TYPE_ERROR, $error_message);
        $this->add_message(self :: TYPE_ERROR, Translation :: get('ContentObjectInstallFailed'));
        $this->add_message(self :: TYPE_ERROR, Translation :: get('PlatformInstallFailed'));
        return false;
    }

    function installation_successful()
    {
        $this->add_message(self :: TYPE_CONFIRM, Translation :: get('InstallSuccess'));
        return true;
    }

    /**
     * Creates an application-specific installer.
     * @param string $application The application for which we want to start the installer.
     * @param string $values The form values passed on by the wizard.
     */
    static function factory($type)
    {
        $class = ContentObject :: type_to_class($type) . 'ContentObjectInstaller';
        
        $file = Path :: get_repository_path() . 'lib/content_object/' . $type . '/install/' . $type . '_installer.class.php';
        if (file_exists($file))
        {
            require_once $file;
            return new $class();
        }
        else
        {
            return false;
        }
    
    }

    abstract function get_path();
}
?>