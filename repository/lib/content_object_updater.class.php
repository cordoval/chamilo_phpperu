<?php
abstract class ContentObjectUpdater
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';
    const UPDATE_SUCCESS = 'success';
    const UPDATE_MESSAGE = 'message';
    
    /**
     * The datamanager which can be used by the installer of the application
     */
    private $data_manager;
    
    /**
     * Message to be displayed upon completion of the update procedure
     */
    private $message;

    private $type;
    /**
     * Constructor
     */
    function ContentObjectUpdater($type)
    {
        $this->data_manager = RepositoryDataManager :: get_instance();
        $this->message = array();
        $this->type = $type;
    }

    function update()
    {
        $dir = $this->get_install_path();
        $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);
        
        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                if (! $this->storage_unit_exist($file))
                {
                    if (! $this->create_storage_unit($file))
                    {
                        return false;
                    }
                }
                else
                {
                    $storage_unit = self :: parse_xml_file($file);
                    $backup = DatabaseBackup::factory('mysql', array($storage_unit['name']), $this->get_data_manager());
                    $output = $backup->backup();
                    $file = Path :: get_temp_path() . 'backup/repository_' . $this->get_content_object() . '_' . time() . '.backup';
                    Filesystem::write_to_file($file, $output, true);
                	$this->add_message(self :: TYPE_WARNING, 'Xml file needed with changes');
                }
            }
        }
        
        if (! $this->configure_content_object())
        {
            return false;
        }
        
        if (method_exists($this, 'update_extra'))
        {
            if (! $this->update_extra())
            {
                return false;
            }
        }
        
        return $this->update_successful();
    }

    public function import_content_object()
    {
        $type = $this->get_content_object();
        $file = Path :: get_repository_path() . 'lib/content_object/' . $type . '/install/example.zip';
        
        if (file_exists($file))
        {
            $condition = new EqualityCondition(User :: PROPERTY_PLATFORMADMIN, 1);
            $user = UserDataManager :: get_instance()->retrieve_users($condition)->next_result();
            $category = RepositoryDataManager :: get_instance();
            
            $import = ContentObjectImport :: factory('cpo', array('tmp_name' => $file), $user, 0);
            if (! $import->import_content_object())
            {
                $message = Translation :: get('ContentObjectImportFailed');
                $this->update_failed($message);
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
        return $this->type;
    }

    function get_content_object_name()
    {
        return Utilities :: underscores_to_camelcase($this->type);
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
            return $this->update_failed(Translation :: get('StorageUnitCreationFailed') . ': <em>' . $storage_unit_info['name'] . '</em>');
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
            $settings[$event->getAttribute('name')] = array('default' => $event->getAttribute('default'), 'user_setting' => $event->getAttribute('user_setting'), 'type' => $event->getAttribute('type'));
        }
        
        return $settings;
    }

    function configure_content_object()
    {
        $content_object = $this->get_content_object();
        $settings_file = $this->get_path() . 'settings.xml';
        
        if (file_exists($settings_file))
        {
            $xml = $this->parse_content_object_settings($settings_file);
            foreach ($xml as $name => $parameters)
            {
                $type = $parameters['type'];
                if ($type == 1)
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
                        $message = Translation :: get('ApplicationConfigurationFailed');
                        $this->update_failed($message);
                    }
                }
                else
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application);
                    $conditions[] = new EqualityCondition(Setting :: PROPERTY_VARIABLE, $name);
                    $condition = new AndCondition($conditions);
                    
                    $settings = AdminDataManager :: get_instance()->retrieve_settings($condition);
                    while ($setting = $settings->next_result())
                    {
                        if (! $setting->delete())
                        {
                            return false;
                        }
                    }
                }
            }
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('SettingsAdded'));
        }
        
        return true;
    }

    function storage_unit_exist($file)
    {
        $storage_unit_info = self :: parse_xml_file($file);
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('StorageUnitExist') . ': <em>' . $storage_unit_info['name'] . '</em>');
        if (! $this->data_manager->storage_unit_exist($storage_unit_info['name']))
        {
            return false;
            //return $this->update_failed(Translation :: get('StorageUnitCreationFailed') . ': <em>' . $storage_unit_info['name'] . '</em>');
        }
        else
        {
            return true;
        }
    }

    function update_failed($error_message)
    {
        $this->add_message(self :: TYPE_ERROR, $error_message);
        $this->add_message(self :: TYPE_ERROR, Translation :: get('ContentObjectInstallFailed'));
        $this->add_message(self :: TYPE_ERROR, Translation :: get('PlatformInstallFailed'));
        return false;
    }

    function update_successful()
    {
        $this->add_message(self :: TYPE_CONFIRM, Translation :: get('InstallSuccess'));
        return true;
    }

    /**
     * Creates an application-specific installer.
     * @param string $application The application for which we want to start the installer.
     * @param string $values The form values passed on by the wizard.
     */
    static function factory($type, $version)
    {
        $version_string = str_replace('.', '', $version);
        
    	$class = ContentObject :: type_to_class($type) . $version_string . 'ContentObjectUpdater';
        
        $file = Path :: get_repository_path() . 'lib/content_object/' . $type . '/update/' . $version . '/' . $type . '_' . $version_string . '_updater.class.php';
        if (file_exists($file))
        {
            require_once $file;
            return new $class($type);
        }
        else
        {
            return false;
        }
    
    }

    abstract function get_path();
    
    abstract function get_install_path();
}
?>