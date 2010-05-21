<?php
/**
 * $Id: installer.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package common
 * @todo Some more common install-functions can be added here. Example: A
 * function which returns the list of xml-files from a given directory.
 */

abstract class Updater
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
     * Message to be displayed upon completion of the installation procedure
     */
    private $message;
    
    private $application;

    /**
     * Constructor
     */
    function Updater($application, $data_manager = null)
    {
        $this->application = $application;
        $this->data_manager = $data_manager;
        $this->message = array();
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
                    $this->add_message(self :: TYPE_WARNING, 'Xml file needed with changes');
                }
            }
        }
        
        if (! $this->configure_application())
        {
            return false;
        }
        
        return $this->update_successful();
    }

    function get_application()
    {
        return $this->application;
    }

    function get_application_name()
    {
        return Utilities :: underscores_to_camelcase($this->application);
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

    function set_data_manager($data_manager)
    {
        $this->data_manager = $data_manager;
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

    /**
     * Parses an XML file and sends the request to the tracking database manager
     * @param String $path
     */
    function create_tracking_storage_unit($path)
    {
        $tdm = TrackingDataManager :: get_instance();
        $storage_unit_info = self :: parse_xml_file($path);
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('StorageUnitCreation') . ': <em>' . $storage_unit_info['name'] . '</em>');
        if (! $tdm->create_storage_unit($storage_unit_info['name'], $storage_unit_info['properties'], $storage_unit_info['indexes']))
        {
            return $this->update_failed(Translation :: get('TrackingStorageUnitCreationFailed') . ': <em>' . $storage_unit_info['name'] . '</em>');
        }
        else
        {
            return true;
        }
    }

    // TODO: It's probably a good idea to write some kind of XML-parsing class that automatically converts the entire thing to a uniform array or object.
    

    function parse_application_events($file)
    {
        $doc = new DOMDocument();
        $result = array();
        
        $doc->load($file);
        $object = $doc->getElementsByTagname('application')->item(0);
        $result['name'] = $object->getAttribute('name');
        
        // Get events
        $events = $doc->getElementsByTagname('event');
        $trackers = array();
        
        foreach ($events as $index => $event)
        {
            $event_name = $event->getAttribute('name');
            $trackers = array();
            
            // Get trackers in event
            $event_trackers = $event->getElementsByTagname('tracker');
            $attributes = array('name', 'active');
            
            foreach ($event_trackers as $index => $event_tracker)
            {
                $property_info = array();
                
                foreach ($attributes as $index => $attribute)
                {
                    if ($event_tracker->hasAttribute($attribute))
                    {
                        $property_info[$attribute] = $event_tracker->getAttribute($attribute);
                    }
                }
                $trackers[$event_tracker->getAttribute('name')] = $property_info;
            }
            
            $result['events'][$event_name]['name'] = $event_name;
            $result['events'][$event_name]['trackers'] = $trackers;
        }
        
        return $result;
    }

	function parse_application_rights($file)
    {
        $doc = new DOMDocument();
        
        $doc->load($file);
        $object = $doc->getElementsByTagname('application')->item(0);
        
        // Get events
        $events = $doc->getElementsByTagname('right');
        $rights = array();
        
        foreach ($events as $index => $event)
        {
            $rights[$event->getAttribute('name')] = array('identifier' => $event->getAttribute('identifier'), 'tree_identifier' => $event->getAttribute('tree_identifier'), 'type' => $event->getAttribute('type'), 'tree_type' => $event->getAttribute('tree_type'), 'update_type' => $event->getAttribute('update_type'), 'parent_identifier' => $event->getAttribute('parent_identifier'), 'parent_tree_identifier' => $event->getAttribute('parent_tree_identifier'), 'parent_type' => $event->getAttribute('parent_type'), 'parent_tree_type' => $event->getAttribute('parent_tree_type'));
        }
        
        return $rights;
    }
    
    function parse_application_settings($file)
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

    function parse_application_reporting($file)
    {
        $doc = new DOMDocument();
        
        $doc->load($file);
        $object = $doc->getElementsByTagname('application')->item(0);
        
        // Get events
        $events = $doc->getElementsByTagname('block');
        $reporting = array();
        
        foreach ($events as $index => $event)
        {
            $reporting['block'][$event->getAttribute('name')] = array('type' => $event->getAttribute('type'));
        }
        
        $events = $doc->getElementsByTagname('template');
        
        foreach ($events as $index => $event)
        {
            $reporting['template'][$event->getAttribute('name')] = array('platform' => $event->getAttribute('platform'), 'type' => $event->getAttribute('type'));
        }
        
        return $reporting;
    }

    function parse_application_webservices($file)
    {
        $doc = new DOMDocument();
        
        $doc->load($file);
        $object = $doc->getElementsByTagname('application')->item(0);
        
        // Get events
        $events = $doc->getElementsByTagname('webservice');
        $webservice = array();
        
        foreach ($events as $index => $event)
        {
            $webservice[$event->getAttribute('name')] = array('type' => $event->getAttribute('type'), 'category' => $event->getAttribute('category'));
        }
        return $webservice;
    }

    /**
     * Function used to register a tracker
     */
    function register_tracker($path, $class)
    {
        $tracker = new TrackerRegistration();
        $class = Utilities :: underscores_to_camelcase($class);
        $tracker->set_class($class);
        $tracker->set_path($path);
        if (! $tracker->create())
        {
            return false;
        }
        
        return $tracker;
    }

    /**
     * Function used to register a tracker to an event
     */
    function register_tracker_to_event($tracker, $event)
    {
        $rel = new EventRelTracker();
        $rel->set_tracker_id($tracker->get_id());
        $rel->set_event_id($event->get_id());
        $rel->set_active(true);
        if ($rel->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Function used to register a block.
     */
    function register_reporting_block($array)
    {
        return Reporting :: create_reporting_block_registration($array);
    }

    function register_reporting_template(&$props)
    {
        return Reporting :: create_reporting_template_registration($props);
    }

    function register_reporting()
    {
        $application = $this->get_application();
        
        $reporting_file = $this->get_path() . 'reporting.xml';
        
        if (file_exists($reporting_file))
        {
            $xml = $this->parse_application_reporting($reporting_file);
            
            foreach ($xml['block'] as $name => $parameters)
            {
                $type = $parameters['type'];
                if ($type == 1)
                {
                    $props = array();
                    $props[ReportingBlockRegistration :: PROPERTY_APPLICATION] = $application;
                    $props[ReportingBlockRegistration :: PROPERTY_BLOCK] = $name;
                    if ($this->register_reporting_block($props))
                    {
                        $this->add_message(self :: TYPE_NORMAL, 'Registered reporting block: <em>' . $props[ReportingBlockRegistration :: PROPERTY_BLOCK] . '</em>');
                    }
                    else
                    {
                        $this->update_failed(Translation :: get('ReportingBlockRegistrationFailed') . ': <em>' . $props[ReportingBlockRegistration :: PROPERTY_BLOCK] . '</em>');
                    }
                
                }
                else
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(ReportingBlockRegistration :: PROPERTY_APPLICATION, $application);
                    $conditions[] = new EqualityCondition(ReportingBlockRegistration :: PROPERTY_BLOCK, $name);
                    $condition = new AndCondition($conditions);
                    
                    if (! ReportingDataManager :: get_instance()->delete_reporting_block_registrations($condition))
                    {
                        $this->update_failed(Translation :: get('DeleteReportingBlockRegistrationFailed') . ': <em>' . $name . '</em>');
                    }
                }
            }
            foreach ($xml['template'] as $name => $parameters)
            {
                $type = $parameters['type'];
                if ($type == 1)
                {
                    $props = array();
                    $props[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = $parameters['platform'];
                    $props[ReportingTemplateRegistration :: PROPERTY_APPLICATION] = $application;
                    $props[ReportingTemplateRegistration :: PROPERTY_TEMPLATE] = $name;
                    if ($this->register_reporting_template($props))
                    {
                        $this->add_message(self :: TYPE_NORMAL, 'Registered reporting template: <em>' . $props[ReportingTemplateRegistration :: PROPERTY_TEMPLATE] . '</em>');
                    }
                    else
                    {
                        $this->update_failed(Translation :: get('ReportingTemplateRegistrationFailed') . ': <em>' . $props[ReportingTemplateRegistration :: PROPERTY_TEMPLATE] . '</em>');
                    }
                }
                else
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, $application);
                    $conditions[] = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_TEMPLATE, $name);
                    $condition = new AndCondition($conditions);
                    
                    if (! ReportingDataManager :: get_instance()->delete_reporting_template_registrations($condition))
                    {
                        $this->update_failed(Translation :: get('DeleteReportingTemplateRegistrationFailed') . ': <em>' . $name . '</em>');
                    }
                }
            }
        }
        return true;
    } //register_reporting

    
    /**
     * Registers the trackers, events and creates the storage units for the trackers
     */
    function register_trackers()
    {
        $this->add_message(self :: TYPE_WARNING, 'Implement when tracking is refactored');
        return true;
    }

    function configure_application()
    {
        $application = $this->get_application();
        
        $settings_file = $this->get_path() . 'settings.xml';
        
        if (file_exists($settings_file))
        {
            $xml = $this->parse_application_settings($settings_file);
            
            foreach ($xml as $name => $parameters)
            {
                $type = $parameters['type'];
                if ($type == 1)
                {
                    $setting = new Setting();
                    $setting->set_application($application);
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
        }
        return true;
    }

    function register_application()
    {
        
        $application = $this->get_application();
        
        if (WebApplication :: is_application($application))
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('RegisteringApplication'));
            
            $application_registration = new Registration();
            $application_registration->set_type(Registration :: TYPE_APPLICATION);
            $application_registration->set_name($application);
            $application_registration->set_status(Registration :: STATUS_ACTIVE);
            
            if (! $application_registration->create())
            {
                return $this->update_failed(Translation :: get('ApplicationRegistrationFailed'));
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Registers the webservices
     */
    function register_webservices()
    {
        $application = $this->get_application();
        
        $webservice_file = $this->get_path() . 'webservices.xml';
        
        if (file_exists($webservice_file))
        {
            $webservices = $this->parse_application_webservices($webservice_file); //contains a list of webservices for this application
            foreach ($webservices as $name => $parameters)
            {
                $condition = new EqualityCondition(WebserviceCategory :: PROPERTY_NAME, $parameters['category']);
                
                $categories = WebserviceDataManager :: get_instance()->retrieve_webservice_categories($condition, null, 1);
                $type = $parameters['type'];
                if ($categories->size() == 0 && $type == 1)
                {
                    $webservice_category = new WebserviceCategory();
                    $webservice_category->set_name($parameters['category']);
                    $webservice_category->set_parent(0);
                    $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceCategoryCreation') . ' : <em>' . $parameters['category'] . '</em>');
                    if (! $webservice_category->create())
                    {
                        return $this->update_failed(Translation :: get('WebserviceCategoryCreationFailed') . ' : <em>' . $parameters['category'] . '</em>');
                    }
                }
                else
                {
                    $webservice_category = $categories->next_result();
                }
                
                if ($type == 1)
                {
                    $webservice = new WebserviceRegistration();
                    $webservice->set_name($name);
                    $webservice->set_description($name);
                    $webservice->set_active(1);
                    $webservice->set_parent($webservice_category->get_id());
                    $webservice->set_application($application);
                    $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceRegistration') . ' : <em>' . $name . '</em>');
                    if (! $webservice->create())
                    {
                        return $this->update_failed(Translation :: get('WebserviceRegistrationFailed') . ' : <em>' . $name . '</em>');
                    }
                }
                else
                {
                	$conditions[] = new EqualityCondition(WebserviceRegistration::PROPERTY_APPLICATION, $application);
                	$conditions[] = new EqualityCondition(WebserviceRegistration::PROPERTY_NAME, $name);
                	$condition = new AndCondition($conditions);
                	
                	if (! WebserviceDataManager::get_instance()->delete_webservices($condition))
                	{
                		return $this->update_failed(Translation :: get('DeleteWebserviceRegistrationFailed') . ' : <em>' . $name . '</em>');
                	}
                }
            }
        }
        return true;    
    }
    
    function register_location()
    {
    	$application = $this->get_application();
        
        $rights_file = $this->get_path() . 'rights_locations.xml';
        
        if (file_exists($rights_file))
        {
            $xml = $this->parse_application_rights($rights_file);
            
            foreach ($xml as $name => $parameters)
            {
                $type = $parameters['update_type'];
                if ($type == 1)
                {
                	$location = new Location();
                    $location->set_application($application);
                    $location->set_location($name);
                    $location->set_identifier($parameters['identifier']);
                    $location->set_tree_identifier($parameters['tree_identifier']);
                    $location->set_type($parameters['type']);
                    $location->set_tree_type($parameters['tree_type']);
                    
                    $parent = RightsUtilities::get_location_by_identifier($application, $parameters['parent_type'], $parameters['parent_identifier'], $parameters['parent_tree_identifier'], $parameters['parent_tree_type']);
				 	$location->set_parent($parent->get_id()); 

                   	if (! $location->create())
                    {
                        $message = Translation :: get('ApplicationConfigurationFailed');
                        $this->update_failed($message);
                    }
                }
                else
                {                  
                    $location = RightsUtilities::get_location_by_identifier($application, $parameters['type'], $parameters['identifier'], $parameters['tree_identifier'], $parameters['tree_type']);
                    $location->delete();
                }
            }
        }
        return true;
    }

    function post_process()
    {
        $application = $this->get_application();
        
        // Parse the Locations XML of the application
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('Rights') . '</span>');
        if (! $this->register_location())
        {
            return $this->update_failed(Translation :: get('LocationsFailed'));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('LocationsAdded'));
        }
        $this->add_message(self :: TYPE_NORMAL, '');
        
        // Handle any and every other thing that needs to happen after
        // the entire kernel was installed
        

        // VARIOUS #1: Tracking
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('Tracking') . '</span>');
        if (! $this->register_trackers())
        {
            return $this->update_failed(Translation :: get('TrackingFailed'));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('TrackingAdded'));
        }
        $this->add_message(self :: TYPE_NORMAL, '');
        
        // VARIOUS #2: Reporting
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('Reporting') . '</span>');
        if (! $this->register_reporting())
        {
            return $this->update_failed(Translation :: get('ReportingFailed'));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ReportingAdded'));
        }
        $this->add_message(self :: TYPE_NORMAL, '');
        
        // VARIOUS #3: Webservices
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('Webservice') . '</span>');
        if (! $this->register_webservices())
        {
            return $this->update_failed(Translation :: get('WebserviceFailed'));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceSucces'));
        }
        $this->add_message(self :: TYPE_NORMAL, '');
        
        // VARIOUS #4: The rest
        if (method_exists($this, 'update_extra'))
        {
            $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('Various') . '</span>');
            if (! $this->update_extra())
            {
                return $this->update_failed(Translation :: get('VariousFailed'));
            }
            else
            {
                $this->add_message(self :: TYPE_NORMAL, Translation :: get('VariousFinished'));
            }
            $this->add_message(self :: TYPE_NORMAL, '');
        }
        return $this->update_successful();
    }

    function update_failed($error_message)
    {
        $this->add_message(self :: TYPE_ERROR, $error_message);
        $this->add_message(self :: TYPE_ERROR, Translation :: get('ApplicationUpdateFailed'));
        $this->add_message(self :: TYPE_ERROR, Translation :: get('PlatformUpdateFailed'));
        return false;
    }

    function update_successful()
    {
        $this->add_message(self :: TYPE_CONFIRM, Translation :: get('UpdateSuccess'));
        return true;
    }

    /**
     * Creates an application-specific installer.
     * @param string $application The application for which we want to start the installer.
     * @param string $values The form values passed on by the wizard.
     */
    static function factory($application, $version)
    {
        $version_string = str_replace('.', '', $version);
        $class = Application :: application_to_class($application) . $version_string . 'Updater';
        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        
        require_once ($base_path . $application . '/update/' . $version . '/' . $application . '_' . $version_string . '_updater.class.php');
        return new $class($application);
    }

    abstract function get_path();

    abstract function get_install_path();

    function extract_xml_file($file)
    {
        return Utilities :: extract_xml_file($file);
    }
}
?>