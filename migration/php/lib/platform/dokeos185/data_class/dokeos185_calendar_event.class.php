<?php
namespace migration;

use common\libraries\Translation;
use repository\RepositoryDataManager;
use common\libraries\Utilities;
use repository\content_object\calendar_event\CalendarEvent;
/**
 * $Id: dokeos185_calendar_event.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Calendar Event
 *
 * @author Sven Vanpoucke
 */
class Dokeos185CalendarEvent extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'calendar_event';

    /**
     * Calendar event properties
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
    function __construct($defaultProperties = array())
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
    function is_valid()
    {
        $this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'calendar_event', $this->get_id()));

        if (!$this->get_id() || !($this->get_title() || $this->get_content()) || !$this->get_item_property() || !$this->get_start_date() || $this->get_item_property()->get_visibility() == 2)
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'calendar_event', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new calendar event
     * @param Course $course the course where the calendar event belongs to
     * @return the new calendar event
     */
    function convert_data()
    {
        $course = $this->get_course();
        $new_user_id = $this->get_id_reference($this->get_item_property()->get_insert_user_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        $new_to_group_id[] = $this->get_id_reference($this->get_item_property()->get_to_group_id(), $this->get_database_name() . '.' . Dokeos185Group::get_table_name());
        $new_to_user_id[] = $this->get_id_reference($this->get_item_property()->get_to_user_id(), 'main_database.user');

        if (!$new_user_id)
        {
            $new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        }

        //calendar event parameters
        $chamilo_calendar_event = new CalendarEvent();

        $chamilo_calendar_event->set_start_date(strtotime($this->get_start_date()));
        $chamilo_calendar_event->set_end_date(strtotime($this->get_end_date()));

        // Category for calendar_events already exists?
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('CalendarEvents'));

        $chamilo_calendar_event->set_parent_id($chamilo_category_id);

        if (!$this->get_title())
        {
            $chamilo_calendar_event->set_title(substr($this->get_content(), 0, 20));
        }
        else
        {
            $chamilo_calendar_event->set_title($this->get_title());
        }

        if (!$this->get_content())
        {
            $chamilo_calendar_event->set_description($this->get_title());
        }
        else
        {
            $content = $this->parse_text_field($this->get_content());
            $chamilo_calendar_event->set_description($content);
        }

        $chamilo_calendar_event->set_owner_id($new_user_id);
        $chamilo_calendar_event->set_creation_date(strtotime($this->get_item_property()->get_insert_date()));
        $chamilo_calendar_event->set_modification_date(strtotime($this->get_item_property()->get_lastedit_date()));

        if ($this->get_item_property()->get_visibility() == 2)
        {
            $chamilo_calendar_event->set_state(1);
        }

        //create announcement in database
        $chamilo_calendar_event->create_all();

        $this->add_resources($chamilo_calendar_event, 'agenda');

        //publication

        $this->create_publication($chamilo_calendar_event, $new_course_code, $new_user_id, 'calendar', 0, $new_to_user_id, $new_to_group_id);

        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'calendar_event', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_calendar_event->get_id())));
        return $chamilo_calendar_event;
    }

    static function get_table_name()
    {
                return Utilities :: camelcase_to_underscores(substr(Utilities :: get_classname_from_namespace(__CLASS__), 9));  ;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

}

?>