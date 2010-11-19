<?php

namespace application\personal_calendar;

use user\UserDataManager;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\WebApplication;
use common\libraries\Application;
use repository\content_object\calendar_event\CalendarEvent;
/**
 * $Id: personal_calendar_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager
 */

/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class PersonalCalendarManager extends WebApplication
{
    const APPLICATION_NAME = 'personal_calendar';

    const PARAM_PERSONAL_CALENDAR_ID = 'personal_calendar';

    const ACTION_BROWSE_CALENDAR = 'browser';
    const ACTION_VIEW_PUBLICATION = 'viewer';
    const ACTION_CREATE_PUBLICATION = 'publisher';
    const ACTION_DELETE_PUBLICATION = 'deleter';
    const ACTION_EDIT_PUBLICATION = 'editor';
    const ACTION_VIEW_ATTACHMENT = 'attachment_viewer';
    const ACTION_EXPORT_ICAL = 'ical_exporter';
    const ACTION_IMPORT_ICAL = 'ical_importer';
    const ACTION_RIGHT_EDITS = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_CALENDAR;

    const ACTION_RENDER_BLOCK = 'block';
    const PARAM_TIME = 'time';
    const PARAM_VIEW = 'view';

    /**
     * Constructor
     * @param int $user_id
     */
    public function __construct($user)
    {
        parent :: __construct($user);
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
        return PersonalCalendarDataManager :: get_events($this, $from_date, $to_date);
    }

    public function get_user_events($from_date, $to_date)
    {
        return PersonalCalendarDataManager :: get_user_events($this, $from_date, $to_date);
    }

    public function get_connector_events($from_date, $to_date)
    {
        return PersonalCalendarDataManager :: get_connector_events($this, $from_date, $to_date);
    }

    public function get_user_shared_events($from_date, $to_date)
    {
        return PersonalCalendarDataManager :: get_user_shared_events($this, $from_date, $to_date);
    }

//    public function render_personal_calendar_events($publications, $from_date, $to_date, $source = self :: APPLICATION_NAME)
//    {
//        $events = array();
//        $query = Request :: post('query');
//
//        while ($publication = $publications->next_result())
//        {
//            $parser = PersonalCalendarEventParser :: factory($this, $publication, $from_date, $to_date);
//            $events = array_merge($events, $parser->get_events());
//        }
//        return $events;
//    }

    /**
     * @see Application::content_object_is_published()
     */
    static public function content_object_is_published($object_id)
    {
        $dm = PersonalCalendarDataManager :: get_instance();
        return $dm->content_object_is_published($object_id);
    }

    /**
     * @see Application::any_content_object_is_published()
     */
    static public function any_content_object_is_published($object_ids)
    {
        $dm = PersonalCalendarDataManager :: get_instance();
        return $dm->any_content_object_is_published($object_ids);
    }

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    static public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        $dm = PersonalCalendarDataManager :: get_instance();
        return $dm->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    static public function get_content_object_publication_attribute($publication_id)
    {
        $dm = PersonalCalendarDataManager :: get_instance();
        return $dm->get_content_object_publication_attribute($publication_id);
    }

    static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PersonalCalendarDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    static public function delete_content_object_publications($object_id)
    {
        $dm = PersonalCalendarDataManager :: get_instance();
        return $dm->delete_content_object_publications($object_id);
    }

    static function delete_content_object_publication($publication_id)
    {
        $dm = PersonalCalendarDataManager :: get_instance();
        return $dm->delete_content_object_publication($publication_id);
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    static public function update_content_object_publication_id($publication_attr)
    {
        return PersonalCalendarDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    /**
     * Inherited
     */
    static function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(CalendarEvent :: get_type_name());

        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(Translation :: get('TypeName'));
            return $locations;
        }

        return array();
    }

    static function publish_content_object($content_object, $location)
    {
        require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_publication.class.php';
        $pub = new PersonalCalendarPublication();
        $pub->set_content_object_id($content_object->get_id());
        $pub->set_publisher($content_object->get_owner_id());
        $pub->create();

        return Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('PersonalCalendarPublication')) , Utilities :: COMMON_LIBRARIES);
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

    function retrieve_personal_calendar_publication($publication_id)
    {
        $pcdm = PersonalCalendarDataManager :: get_instance();
        return $pcdm->retrieve_personal_calendar_publication($publication_id);
    }

    function retrieve_task_publication($publication_id)
    {
        $pcdm = PersonalCalendarDataManager :: get_instance();
        return $pcdm->retrieve_personal_calendar_publication($publication_id);
    }

    function get_publication_deleting_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_PERSONAL_CALENDAR_ID => $publication->get_id()));
    }

    function get_publication_editing_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION, self :: PARAM_PERSONAL_CALENDAR_ID => $publication->get_id()));
    }

    function get_publication_viewing_url($publication)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_VIEW_PUBLICATION;
        $parameters[self :: PARAM_PERSONAL_CALENDAR_ID] = $publication->get_id();
        $parameters[Application :: PARAM_APPLICATION] = self :: APPLICATION_NAME;

        return $this->get_link($parameters);
    }

    function get_ical_export_url($publication)
    {
        $parameters = array();
        $parameters[self :: PARAM_PERSONAL_CALENDAR_ID] = $publication->get_id();
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

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
    
    static public function __autoload($classname)
    {
        $list = array(
        'personal_calendar_event' => 'personal_calendar_event.class.php', 
        'personal_calendar_data_manager' =>'personal_calendar_data_manager.class.php', 
        'personal_calendar_event_parser' => 'personal_calendar_event_parser.class.php', 
        'personal_calendar_connector' => 'personal_calendar_connector.class.php',
        '/connector/personal_calendar_connector' => 'personal_calendar_weblcms_connector.class.php',
        'personal_calendar_data_manager' => 'personal_calendar_data_manager.class.php',
        'personal_calendar_publication' => 'personal_calendar_publication.class.php',
        'personal_calendar_publication_user' => 'personal_calendar_publication_user.class.php',
        'personal_calendar_publication_group' => 'personal_calendar_publication_group.class.php',
        'personal_calendar_data_manager_interface' => 'personal_calendar_data_manager_interface.class.php',
        'personal_calendar_publication_form' => 'personal_calendar_publication_form.class.php',
        'personal_calendar_renderer' => 'personal_calendar_renderer.class.php',
        'personal_calendar_manager/personal_calendar_manager' => 'personal_calendar_manager.class.php');
        
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('personal_calendar') . $url;
            return true;
        }
        
        return false;
    }
}
?>