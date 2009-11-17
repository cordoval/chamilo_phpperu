<?php

/**
 * $Id: dokeos185_calendar_event.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_calendar_event.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/calendar_event/calendar_event.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once 'dokeos185_item_property.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Calendar Event
 *
 * @author Sven Vanpoucke
 */
class Dokeos185CalendarEvent extends ImportCalendarEvent
{
    /**
     * Migration data manager
     */
    private static $mgdm;
    
    private $item_property;
    
    /**
     * Announcement properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_START_DATE = 'start_date';
    const PROPERTY_END_DATE = 'end_date';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Calender Event object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185CalendarEvent($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT, self :: PROPERTY_START_DATE, self :: PROPERTY_END_DATE);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this calendar event.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this calendar event.
     * @return string the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the content of this calendar event.
     * @return string the content.
     */
    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    /**
     * Returns the start_date of this calendar event.
     * @return date the start_date.
     */
    function get_start_date()
    {
        return $this->get_default_property(self :: PROPERTY_START_DATE);
    }

    /**
     * Returns the end_date of this calendar event.
     * @return date the end_date.
     */
    function get_end_date()
    {
        return $this->get_default_property(self :: PROPERTY_END_DATE);
    }

    /**
     * Check if the calendar event is valid
     * @param Course $course the course where the calendar event belongs to
     * @return true if the blog is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        $old_mgdm = $array['old_mgdm'];
        $this->item_property = $old_mgdm->get_item_property($course->get_db_name(), 'calendar_event', $this->get_id());
        
        if (! $this->get_id() || ! ($this->get_title() || $this->get_content()) || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.calendar_event');
            return false;
        }
        return true;
    }

    /**
     * Convert to new calendar event
     * @param Course $course the course where the calendar event belongs to
     * @return the new calendar event
     */
    function convert_to_lcms($array)
    {
        $mgdm = MigrationDataManager :: get_instance();
        $course = $array['course'];
        $new_user_id = $mgdm->get_id_reference($this->item_property->get_insert_user_id(), 'user_user');
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //calendar event parameters
        $lcms_calendar_event = new CalendarEvent();
        
        $lcms_calendar_event->set_start_date($mgdm->make_unix_time($this->get_start_date()));
        $lcms_calendar_event->set_end_date($mgdm->make_unix_time($this->get_end_date()));
        
        // Category for calendar_events already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('calendar_events'));
        if (! $lcms_category_id)
        {
            
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($new_user_id);
            $lcms_repository_category->set_name(Translation :: get('calenderEvent'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_calendar_event->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_calendar_event->set_parent_id($lcms_category_id);
        }
        
        if (! $this->get_title())
            $lcms_calendar_event->set_title(substr($this->get_content(), 0, 20));
        else
            $lcms_calendar_event->set_title($this->get_title());
        
        if (! $this->get_content())
            $lcms_calendar_event->set_description($this->get_title());
        else
            $lcms_calendar_event->set_description($this->get_content());
        
        $lcms_calendar_event->set_owner_id($new_user_id);
        $lcms_calendar_event->set_creation_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
        $lcms_calendar_event->set_modification_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
        
        if ($this->item_property->get_visibility() == 2)
            $lcms_calendar_event->set_state(1);
            
        //create announcement in database
        $lcms_calendar_event->create_all();
        
        //publication
        if ($this->item_property->get_visibility() <= 1)
        {
            $publication = new ContentObjectPublication();
            
            $publication->set_content_object($lcms_calendar_event);
            $publication->set_course_id($new_course_code);
            $publication->set_publisher_id($new_user_id);
            $publication->set_tool('calendar_event');
            $publication->set_category_id(0);
            //$publication->set_from_date($mgdm->make_unix_time($this->item_property->get_start_visible()));
            //$publication->set_to_date($mgdm->make_unix_time($this->item_property->get_end_visible()));
            $publication->set_from_date(0);
            $publication->set_to_date(0);
            $publication->set_publication_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
            $publication->set_modified_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
            //$publication->set_modified_date(0);
            //$publication->set_display_order_index($this->get_display_order());
            $publication->set_display_order_index(0);
            $publication->set_email_sent(0);
            
            $publication->set_hidden($this->item_property->get_visibility() == 1 ? 0 : 1);
            
            //create publication in database
            $publication->create();
        }
        
        return $lcms_calendar_event;
    }

    /**
     * Retrieve all calendar events from the database
     * @param array $parameters parameters for the retrieval
     * @return array of calendar events
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'calendar_event';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'calendar_event';
        $classname = 'Dokeos185CalendarEvent';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'calendar_event';
        return $array;
    }
}
?>
