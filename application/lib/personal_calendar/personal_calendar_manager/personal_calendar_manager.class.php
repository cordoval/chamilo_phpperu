<?php
/**
 * $Id: personal_calendar_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager
 */
require_once dirname(__FILE__) . '/personal_calendar_manager_component.class.php';
require_once dirname(__FILE__) . '/../connector/personal_calendar_weblcms_connector.class.php';
require_once dirname(__FILE__) . '/../personal_calendar_event.class.php';
require_once dirname(__FILE__) . '/../personal_calendar_data_manager.class.php';
require_once dirname(__FILE__) . '/../personal_calendar_block.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class PersonalCalendarManager extends WebApplication
{
    const APPLICATION_NAME = 'personal_calendar';
    
    const PARAM_CALENDAR_EVENT_ID = 'calendar_event';
    
    const ACTION_BROWSE_CALENDAR = 'browse';
    const ACTION_VIEW_PUBLICATION = 'view';
    const ACTION_CREATE_PUBLICATION = 'publish';
    const ACTION_DELETE_PUBLICATION = 'delete';
    const ACTION_EDIT_PUBLICATION = 'edit';
    const ACTION_VIEW_ATTACHMENT = 'view_attachment';
    const ACTION_EXPORT_ICAL = 'export_ical';
    const ACTION_IMPORT_ICAL = 'import_ical';
    
    const ACTION_RENDER_BLOCK = 'block';

    /**
     * Constructor
     * @param int $user_id
     */
    public function PersonalCalendarManager($user)
    {
        parent :: __construct($user);
    }

    /**
     * Runs the personal calendar application
     */
    public function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_BROWSE_CALENDAR :
                $component = PersonalCalendarManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_VIEW_PUBLICATION :
                $component = PersonalCalendarManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_CREATE_PUBLICATION :
                $component = PersonalCalendarManagerComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_DELETE_PUBLICATION :
                $component = PersonalCalendarManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_EDIT_PUBLICATION :
                $component = PersonalCalendarManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_VIEW_ATTACHMENT :
                $component = PersonalCalendarManagerComponent :: factory('AttachmentViewer', $this);
                break;
            case self :: ACTION_EXPORT_ICAL :
                $component = PersonalCalendarManagerComponent :: factory('IcalExporter', $this);
                break;
            case self :: ACTION_IMPORT_ICAL :
                $component = PersonalCalendarManagerComponent :: factory('IcalImporter', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_CALENDAR);
                $component = PersonalCalendarManagerComponent :: factory('Browser', $this);
        }
        $component->run();
    }

    /**
     * Renders the weblcms block and returns it.
     */
    function render_block($block)
    {
        $personal_calendar_block = PersonalCalendarBlock :: factory($this, $block);
        return $personal_calendar_block->run();
    }

    /**
     * Gets the events
     * @param int $from_date
     * @param int $to_date
     */
    public function get_events($from_date, $to_date)
    {
        $events = $this->get_user_events($from_date, $to_date);
        $events = array_merge($events, $this->get_connector_events($from_date, $to_date));
        $events = array_merge($events, $this->get_user_shared_events($from_date, $to_date));
        return $events;
    }

    public function get_user_events($from_date, $to_date)
    {
        
        $dm = PersonalCalendarDatamanager :: get_instance();
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_PUBLISHER, $this->get_user_id());
        $publications = $dm->retrieve_calendar_event_publications($condition);
        
        //$query = Request :: post('query');
        

        return $this->render_personal_calendar_events($publications, $from_date, $to_date);
    }

    public function get_connector_events($from_date, $to_date)
    {
        $events = array();
        
        $path = dirname(__FILE__) . '/../connector/';
        $files = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES, false);
        foreach ($files as $file)
        {
            $application = str_replace('_connector.class.php', '', $file);
            $application = str_replace(PersonalCalendarManager :: APPLICATION_NAME . '_', '', $application);
            $application = Utilities :: camelcase_to_underscores($application);
            
            if (WebApplication :: is_active($application))
            {
                $file_class = split('.class.php', $file);
                require_once dirname(__FILE__) . '/../connector/' . $file;
                $class = Utilities :: underscores_to_camelcase($file_class[0]);
                
                $connector = new $class();
                $events = array_merge($events, $connector->get_events($this->get_user(), $from_date, $to_date));
            }
        }
        
        return $events;
    }

    public function get_user_shared_events($from_date, $to_date)
    {
        $events = array();
        $user = $this->get_user();
        $user_groups = $user->get_groups(true);
        
        $pcdm = PersonalCalendarDatamanager :: get_instance();
        $conditions = array();
        $conditions[] = new EqualityCondition('user_id', $this->get_user_id(), $pcdm->get_database()->get_alias('publication_user'));
        if (count($user_groups) > 0)
        {
            $conditions[] = new InCondition('group_id', $user_groups, $pcdm->get_database()->get_alias('publication_group'));
        }
        $condition = new OrCondition($conditions);
        $publications = $pcdm->retrieve_shared_calendar_event_publications($condition);
        
        return $this->render_personal_calendar_events($publications, $from_date, $to_date, 'SharedEvents');
    }

    public function render_personal_calendar_events($publications, $from_date, $to_date, $source = self :: APPLICATION_NAME)
    {
        $events = array();
        $query = Request :: post('query');
        
        while ($publication = $publications->next_result())
        {
            $object = $publication->get_publication_object();
            $publisher = $publication->get_publisher();
            $publishing_user = $publication->get_publication_publisher();
            
            if (isset($query) && $query != '')
            {
                if ((stripos($object->get_title(), $query) === false) && (stripos($object->get_description(), $query) === false))
                    continue;
            }
            
            if ($object->repeats())
            {
                $repeats = $object->get_repeats($from_date, $to_date);
                
                foreach ($repeats as $repeat)
                {
                    $event = new PersonalCalendarEvent();
                    $event->set_start_date($repeat->get_start_date());
                    $event->set_end_date($repeat->get_end_date());
                    $event->set_url($this->get_publication_viewing_url($publication));
                    
                    // Check whether it's a shared or regular publication
                    if ($publisher != $this->get_user_id())
                    {
                        $event->set_title($object->get_title() . ' [' . $publishing_user->get_fullname() . ']');
                    }
                    else
                    {
                        $event->set_title($object->get_title());
                    }
                    
                    $event->set_content($repeat->get_description());
                    $event->set_source($source);
                    $event->set_id($publication->get_id());
                    $events[] = $event;
                }
            }
            elseif ($object->get_start_date() >= $from_date && $object->get_start_date() <= $to_date)
            {
                $event = new PersonalCalendarEvent();
                $event->set_start_date($object->get_start_date());
                $event->set_end_date($object->get_end_date());
                $event->set_url($this->get_publication_viewing_url($publication));
                
                // Check whether it's a shared or regular publication
                if ($publisher != $this->get_user_id())
                {
                    $event->set_title($object->get_title() . ' [' . $publishing_user->get_fullname() . ']');
                }
                else
                {
                    $event->set_title($object->get_title());
                }
                
                $event->set_content($object->get_description());
                $event->set_source($source);
                $event->set_id($publication->get_id());
                $events[] = $event;
            }
        }
        
        return $events;
    }

    /**
     * @see Application::content_object_is_published()
     */
    public function content_object_is_published($object_id)
    {
        $dm = PersonalCalendarDatamanager :: get_instance();
        return $dm->content_object_is_published($object_id);
    }

    /**
     * @see Application::any_content_object_is_published()
     */
    public function any_content_object_is_published($object_ids)
    {
        $dm = PersonalCalendarDatamanager :: get_instance();
        return $dm->any_content_object_is_published($object_ids);
    }

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        $dm = PersonalCalendarDatamanager :: get_instance();
        return $dm->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    public function get_content_object_publication_attribute($publication_id)
    {
        $dm = PersonalCalendarDatamanager :: get_instance();
        return $dm->get_content_object_publication_attribute($publication_id);
    }

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PersonalCalendarDatamanager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    public function delete_content_object_publications($object_id)
    {
        $dm = PersonalCalendarDatamanager :: get_instance();
        return $dm->delete_content_object_publications($object_id);
    }
    
	function delete_content_object_publication($publication_id)
    {
    	$dm = PersonalCalendarDatamanager :: get_instance();
        return $dm->delete_content_object_publication($publication_id);
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    public function update_content_object_publication_id($publication_attr)
    {
        return PersonalCalendarDatamanager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    /**
     * Inherited
     */
    function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array('calendar_event');
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(__CLASS__);
            return $locations;
        }
        
        return array();
    }

    function publish_content_object($content_object, $location)
    {
        require_once dirname(__FILE__) . '/../calendar_event_publication.class.php';
        $pub = new CalendarEventPublication();
        $pub->set_calendar_event($content_object->get_id());
        $pub->set_publisher($content_object->get_owner_id());
        $pub->create();
        
        return Translation :: get('PublicationCreated');
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function retrieve_calendar_event_publication($publication_id)
    {
        $pcdm = PersonalCalendarDataManager :: get_instance();
        return $pcdm->retrieve_calendar_event_publication($publication_id);
    }

    function get_publication_deleting_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_CALENDAR_EVENT_ID => $publication->get_id()));
    }

    function get_publication_editing_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION, self :: PARAM_CALENDAR_EVENT_ID => $publication->get_id()));
    }

    function get_publication_viewing_url($publication)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_VIEW_PUBLICATION;
        $parameters[self :: PARAM_CALENDAR_EVENT_ID] = $publication->get_id();
        $parameters[Application :: PARAM_APPLICATION] = self :: APPLICATION_NAME;
        
        return $this->get_link($parameters);
    }

    function get_ical_export_url($publication)
    {
        $parameters = array();
        $parameters[self :: PARAM_CALENDAR_EVENT_ID] = $publication->get_id();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_EXPORT_ICAL;
        
        return $this->get_url($parameters);
    }

    function get_ical_import_url()
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_IMPORT_ICAL;
        
        return $this->get_url($parameters);
    }

    function get_user_info($user_id)
    {
        return UserDataManager :: get_instance()->retrieve_user($user_id);
    }
}
?>