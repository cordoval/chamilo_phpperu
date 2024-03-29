<?php
namespace common\libraries;

use rights\RightsManager;
use webservice\WebserviceManager;
use tracking\TrackingManager;
use reporting\ReportingManager;
use DOMDocument;
use rights\RightsUtilities;
use admin\Setting;
use tracking\TrackingDataManager;
use tracking\Event;
use tracking\TrackerRegistration;
use tracking\EventRelTracker;
use reporting\ReportingBlockRegistration;
use reporting\ReportingTemplateRegistration;
use reporting\Reporting;
use webservice\WebserviceCategory;
use webservice\WebserviceRegistration;

use admin\Registration;
use admin\PackageInfo;
/**
 * $Id: installer.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package common
 * @todo Some more common install-functions can be added here. Example: A
 * function which returns the list of xml-files from a given directory.
 */

abstract class Installer
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
     * Form values passed on from the installation wizard
     */
    private $form_values;

    /**
     * Constructor
     */
    function __construct($form_values, $data_manager = null)
    {
        $this->form_values = $form_values;
        $this->data_manager = $data_manager;
        $this->message = array();
    }

    function install()
    {
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

        if (! $this->configure_application())
        {
            return false;
        }

        //		if (method_exists($this, 'install_extra'))
        //		{
        //			if (!$this->install_extra())
        //			{
        //				return false;
        //			}
        //		}


        return $this->installation_successful();
    }

    function get_application()
    {
        $application_class = $this->get_application_name();
        $application = Utilities :: camelcase_to_underscores($application_class);

        return $application;
    }

    function get_application_name()
    {
        $class = Utilities :: get_classname_from_object($this);
        $application_class = str_replace('Installer', '', $class);

        return $application_class;
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

    function set_form_values($form_values)
    {
        $this->form_values = $form_values;
    }

    function get_form_values()
    {
        return $this->form_values;
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
    {
        $storage_unit_info = self :: parse_xml_file($path);
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('StorageUnitCreation', null, 'install') . ': <em>' . $storage_unit_info['name'] . '</em>');
        if (! $this->data_manager->create_storage_unit($storage_unit_info['name'], $storage_unit_info['properties'], $storage_unit_info['indexes']))
        {
            return false;
            return $this->installation_failed(Translation :: get('StorageUnitCreationFailed', null, 'install') . ': <em>' . $storage_unit_info['name'] . '</em>');
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
        $this->add_message(self :: TYPE_NORMAL, Translation :: get('StorageUnitCreation', null, 'install') . ': <em>' . $storage_unit_info['name'] . '</em>');
        if (! $tdm->create_storage_unit($storage_unit_info['name'], $storage_unit_info['properties'], $storage_unit_info['indexes']))
        {
            return $this->installation_failed(Translation :: get('TrackingStorageUnitCreationFailed', null, 'install') . ': <em>' . $storage_unit_info['name'] . '</em>');
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
            $settings[$event->getAttribute('name')] = array('default' => $event->getAttribute('default'), 'user_setting' => $event->getAttribute('user_setting'));
        }

        return $settings;
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

        $is_web_application = WebApplication :: is_application($application);
        $base_path = ($is_web_application ? WebApplication :: get_application_class_path($application) : CoreApplication :: get_application_class_path($application));
        $namespace = $is_web_application ? 'application\\' . $application : $application;
        $namespace .= '\\';

        $dirblock = $base_path . 'reporting/blocks';
        if (is_dir($dirblock))
        {
            $files = Filesystem :: get_directory_content($dirblock, Filesystem :: LIST_FILES);
            if (count($files) > 0)
            {
                foreach ($files as $file)
                {
                    if ((substr($file, - 16) == '_block.class.php'))
                    {
                        require_once ($file);
                        $bla = explode('.', basename($file));
                        $classname = $namespace . Utilities :: underscores_to_camelcase($bla[0]);
                        $block = $bla[0];
                        //$method = new ReflectionMethod($classname, 'get_properties');
                        //$props = $method->invoke(null);
                        $props = array();
                        $props[ReportingBlockRegistration :: PROPERTY_APPLICATION] = $application;
                        $props[ReportingBlockRegistration :: PROPERTY_BLOCK] = $block;
                        if ($this->register_reporting_block($props))
                        {
                            $this->add_message(self :: TYPE_NORMAL, Translation :: get('RegisteredBlock', null, ReportingManager :: APPLICATION_NAME) . ': <em>' . $props[ReportingBlockRegistration :: PROPERTY_BLOCK] . '</em>');
                        }
                        else
                        {
                            $this->installation_failed(Translation :: get('ReportingBlockRegistrationFailed', null, 'install') . ': <em>' . $props[ReportingBlockRegistration :: PROPERTY_BLOCK] . '</em>');
                        }
                    }
                }
            }
        }

        $dir = $base_path . 'reporting/templates';
        if (is_dir($dir))
        {
            $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);

            if (count($files) > 0)
            {
                foreach ($files as $file)
                {
                    if ((substr($file, - 19) == '_template.class.php'))
                    {
                        require_once ($file);
                        $bla = explode('.', basename($file));
                        $classname = $namespace . Utilities :: underscores_to_camelcase($bla[0]);
                        $template = $bla[0];
                        $props = array();
                        $props[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = call_user_func(array($classname, 'is_platform'));
                        $props[ReportingTemplateRegistration :: PROPERTY_APPLICATION] = $application;
                        $props[ReportingTemplateRegistration :: PROPERTY_TEMPLATE] = $template;
                        if ($this->register_reporting_template($props))
                        {
                            $this->add_message(self :: TYPE_NORMAL, Translation :: get('RegisteredTemplate', null, ReportingManager :: APPLICATION_NAME) . ': <em>' . $props[ReportingTemplateRegistration :: PROPERTY_TEMPLATE] . '</em>');
                        }
                        else
                        {
                            $this->installation_failed(Translation :: get('ReportingTemplateRegistrationFailed', null, 'install') . ': <em>' . $props[ReportingTemplateRegistration :: PROPERTY_TEMPLATE] . '</em>');
                        }
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
        $application = $this->get_application();

        $base_path = (WebApplication :: is_application($application) ? WebApplication :: get_application_class_path($application) : CoreApplication :: get_application_class_path($application));

        $dir = $base_path . 'trackers/tracker_tables/';
        $files = array();

        if (is_dir($dir))
        {
            $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);

            if (count($files) > 0)
            {
                foreach ($files as $file)
                {
                    if ((substr($file, - 3) == 'xml'))
                    {
                        $this->create_tracking_storage_unit($file);
                    }
                }
            }
        }

        $trackers_file = $base_path . 'trackers/trackers_' . $application . '.xml';

        if (file_exists($trackers_file))
        {
            $xml = $this->parse_application_events($trackers_file);

            if (isset($xml['events']))
            {
                $registered_trackers = array();

                foreach ($xml['events'] as $event_name => $event_properties)
                {
                    $the_event = new Event();
                    $the_event->set_name($event_properties['name']);
                    $the_event->set_active(true);
                    $the_event->set_block($xml['name']);

                    if (! $the_event->create())
                    {
                        $this->installation_failed(Translation :: get('EventCreationFailed', null, TrackingManager :: APPLICATION_NAME) . ': <em>' . $event_properties['name'] . '</em>');
                    }

                    foreach ($event_properties['trackers'] as $tracker_name => $tracker_properties)
                    {
                        if (! array_key_exists($tracker_properties['name'], $registered_trackers))
                        {
                            $the_tracker = new TrackerRegistration();
                            $the_tracker->set_tracker($tracker_properties['name'] . '_tracker');
                            $the_tracker->set_application($xml['name']);

                            if (! $the_tracker->create())
                            {
                                $this->installation_failed(Translation :: get('TrackerRegistrationFailed', null, TrackingManager :: APPLICATION_NAME) . ': <em>' . $tracker_properties['name'] . '</em>');
                            }

                            $registered_trackers[$tracker_properties['name']] = $the_tracker;
                        }

                        $rel = new EventRelTracker();
                        $rel->set_tracker_id($registered_trackers[$tracker_properties['name']]->get_id());
                        $rel->set_event_id($the_event->get_id());
                        $rel->set_active(true);
                        if ($rel->create())
                        {
                            $this->add_message(self :: TYPE_NORMAL, Translation :: get('TrackersRegisteredToEvent', null, TrackingManager :: APPLICATION_NAME) . ': <em>' . $event_properties['name'] . ' + ' . $tracker_properties['name'] . '</em>');
                        }
                        else
                        {
                            $this->installation_failed(Translation :: get('TrackerRegistrationToEventFailed', null, 'install') . ': <em>' . $event_properties['name'] . '</em>');
                        }
                    }
                }
            }
            elseif (count($files) > 0)
            {
                //$warning_message = Translation :: get('UnlinkedTrackers', null, 'install') . ': <em>' . Translation :: get('CheckUnlinkedTrackers', null, TrackingManager :: APPLICATION_NAME) . ' ' . $path . '</em>';
            //$this->add_message(self :: TYPE_WARNING, $warning_message);
            }
        }
        elseif (count($files) > 0)
        {
            //$warning_message = Translation :: get('UnlinkedTrackers', null, 'install') . ': <em>' . Translation :: get('CheckUnlinkedTrackers', null, TrackingManager :: APPLICATION_NAME) . ' ' . $path . '</em>';
        //$this->add_message(self :: TYPE_WARNING, $warning_message);
        }

        return true;
    }

    function configure_application()
    {

        $application = $this->get_application();

        $base_path = (WebApplication :: is_application($application) ? WebApplication :: get_application_class_path($application) : CoreApplication :: get_application_class_path($application));

        $settings_file = $base_path . 'settings/settings_' . $application . '.xml';

        if (file_exists($settings_file))
        {
            $xml = $this->parse_application_settings($settings_file);

            foreach ($xml as $name => $parameters)
            {
                $setting = new Setting();
                $setting->set_application($application);
                $setting->set_variable($name);
                $setting->set_value($parameters['default']);

                $user_setting = $parameters['user_setting'];
                if ($user_setting)
                {
                    $setting->set_user_setting($user_setting);
                }
                else
                {
                    $setting->set_user_setting(0);
                }

                if (! $setting->create())
                {
                    $message = Translation :: get('ApplicationConfigurationFailed', null, 'install');
                    $this->installation_failed($message);
                }
            }
        }

        return true;
    }

    function register_application()
    {
        $application = $this->get_application();

        $this->add_message(self :: TYPE_NORMAL, Translation :: get('RegisteringApplication', null, 'install'));

        if (WebApplication :: is_application($application))
        {
            $package_info = PackageInfo :: factory(Registration :: TYPE_APPLICATION, $application);
        }
        else
        {
            $package_info = PackageInfo :: factory(Registration :: TYPE_CORE, $application);
        }
        $package_info = $package_info->get_package();

        $application_registration = new Registration();
        $application_registration->set_type($package_info->get_section());
        $application_registration->set_name($package_info->get_code());
        $application_registration->set_category($package_info->get_category());
        $application_registration->set_version($package_info->get_version());
        $application_registration->set_status(Registration :: STATUS_ACTIVE);

        if (! $application_registration->create())
        {
            return $this->installation_failed(Translation :: get('ApplicationRegistrationFailed', null, 'install'));
        }
        else
        {
            return true;
        }
    }

    /** deprecated
     * Registers the webservices
     */
    function register_webservices()
    {
        $methods = array('get', 'get_list', 'create', 'update', 'delete');
        
        $application = $this->get_application();
        $namespace = Application :: determine_namespace($application);
        
        $base_path = (WebApplication :: is_application($application) ? WebApplication :: get_application_class_path($application) : CoreApplication :: get_application_class_path($application));
        $path = $base_path . 'webservices/';

        if(!file_exists($path))
        {
            return true;
        }

        $folders = FileSystem :: get_directory_content($path, FileSystem :: LIST_DIRECTORIES, false);
        if(count($folders) > 0)
        {
            $application_webservice_category = new WebserviceCategory();
            $application_webservice_category->set_name(Translation :: get('TypeName', null, $namespace));
            if(!$application_webservice_category->create())
            {
                return $this->installation_failed(Translation :: get('WebserviceCategoryCreationFailed', null, WebserviceManager :: APPLICATION_NAME) . ' : <em>' . $application_webservice_category->get_name() . '</em>');
            }
            else
            {
                $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceCategoryCreated', null, WebserviceManager :: APPLICATION_NAME) . ': <em>' . $application_webservice_category->get_name() . '</em>');
            }
        }

        foreach($folders as $folder)
        {
            $camelcase_folder = Utilities :: underscores_to_camelcase($folder);
            
            $object_webservice_category = new WebserviceCategory();
            $object_webservice_category->set_name(Translation :: get($camelcase_folder, null, $namespace));
            $object_webservice_category->set_parent($application_webservice_category->get_id());
            if(!$object_webservice_category->create())
            {
                return $this->installation_failed(Translation :: get('WebserviceCategoryCreationFailed', null, WebserviceManager :: APPLICATION_NAME) . ' : <em>' . $object_webservice_category->get_name() . '</em>');
            }
            else
            {
                $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceCategoryCreated', null, WebserviceManager :: APPLICATION_NAME) . ': <em>' . $object_webservice_category->get_name() . '</em>');
            }

            $file = $path . $folder . '/webservice_handler.class.php';
            if(file_exists($file))
            {
                require_once $file;
                $class = $namespace . '\\' . Utilities :: underscores_to_camelcase($folder) . 'WebserviceHandler';
                foreach($methods as $method)
                {
                    if(method_exists($class, $method))
                    {
                        $camelcase_method = Utilities :: underscores_to_camelcase($method);

                        $webservice = new WebserviceRegistration();
                        $webservice->set_name(Translation :: get($camelcase_method, null, WebserviceManager :: APPLICATION_NAME));
                        $webservice->set_description(Translation :: get($camelcase_folder . $camelcase_method . 'Description', null, $namespace));
                        $webservice->set_active(1);
                        $webservice->set_category($object_webservice_category->get_id());
                        $webservice->set_code($application . '_' . $folder . '_' . $method);
                        if(!$webservice->create())
                        {
                            return $this->installation_failed(Translation :: get('WebserviceCreationFailed', null, WebserviceManager :: APPLICATION_NAME) . ' : <em>' . $webservice->get_name() . '</em>');
                        }
                        else
                        {
                            $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceCreated', null, WebserviceManager :: APPLICATION_NAME) . ': <em>' . $webservice->get_name() . '</em>');
                        }
                    }
                }
            }
        }


        return true;

    }

    function post_process()
    {
        if (! $this->register_application())
        {
            return false;
        }

        $application = $this->get_application();

        // Parse the Locations XML of the application
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('TypeName', null, RightsManager :: APPLICATION_NAME) . '</span>');
        if (! RightsUtilities :: create_application_root_location($application))
        {
            return $this->installation_failed(Translation :: get('LocationsFailed', null, RightsManager :: APPLICATION_NAME));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('LocationsAdded', null, RightsManager :: APPLICATION_NAME));
        }
        $this->add_message(self :: TYPE_NORMAL, '');

        // Handle any and every other thing that needs to happen after
        // the entire kernel was installed


        // VARIOUS #1: Tracking
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('TypeName', null, TrackingManager :: APPLICATION_NAME) . '</span>');
        if (! $this->register_trackers())
        {
            return $this->installation_failed(Translation :: get('TrackingFailed', null, TrackingManager :: APPLICATION_NAME));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('TrackingAdded', null, TrackingManager :: APPLICATION_NAME));
        }
        $this->add_message(self :: TYPE_NORMAL, '');

        // VARIOUS #2: Reporting
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('TypeName', null, ReportingManager :: APPLICATION_NAME) . '</span>');
        if (! $this->register_reporting())
        {
            return $this->installation_failed(Translation :: get('ReportingFailed', null, ReportingManager :: APPLICATION_NAME));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ReportingAdded', null, ReportingManager :: APPLICATION_NAME));
        }
        $this->add_message(self :: TYPE_NORMAL, '');

        // VARIOUS #3: Webservices
        $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('TypeName', null, WebserviceManager :: APPLICATION_NAME) . '</span>');
        if (! $this->register_webservices())
        {
            return $this->installation_failed(Translation :: get('WebserviceFailed', null, WebserviceManager :: APPLICATION_NAME));
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceSucces', null, WebserviceManager :: APPLICATION_NAME));
        }
        $this->add_message(self :: TYPE_NORMAL, '');

        // VARIOUS #4: The rest
        if (method_exists($this, 'install_extra'))
        {
            $this->add_message(self :: TYPE_NORMAL, '<span class="subtitle">' . Translation :: get('Various', null, 'install') . '</span>');
            if (! $this->install_extra())
            {
                return $this->installation_failed(Translation :: get('VariousFailed', null, 'install'));
            }
            else
            {
                $this->add_message(self :: TYPE_NORMAL, Translation :: get('VariousFinished', null, 'install'));
            }
            $this->add_message(self :: TYPE_NORMAL, '');
        }
        return $this->installation_successful();
    }

    function installation_failed($error_message)
    {
        $this->add_message(self :: TYPE_ERROR, $error_message);
        $this->add_message(self :: TYPE_ERROR, Translation :: get('ApplicationInstallFailed', null, 'install'));
        $this->add_message(self :: TYPE_ERROR, Translation :: get('PlatformInstallFailed', null, 'install'));
        return false;
    }

    function installation_successful()
    {
        $this->add_message(self :: TYPE_CONFIRM, Translation :: get('InstallSuccess', null, 'install'));
        return true;
    }

    /**
     * Creates an application-specific installer.
     * @param string $application The application for which we want to start the installer.
     * @param string $values The form values passed on by the wizard.
     */
    static function factory($application, $values)
    {
        $class = Application :: determine_namespace($application) . '\\' . Application :: application_to_class($application) . 'Installer';
        $base_path = (WebApplication :: is_application($application) ? WebApplication :: get_application_class_path($application) : CoreApplication :: get_application_class_path($application));

        require_once ($base_path . 'install/' . $application . '_installer.class.php');
        return new $class($values);
    }

    abstract function get_path();

    function extract_xml_file($file)
    {
        return Utilities :: extract_xml_file($file);
    }
}
?>