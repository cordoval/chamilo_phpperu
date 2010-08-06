<?php

/**
 * $Id: dokeos185_personal_agenda.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once dirname(__FILE__) . '/../dokeos185_data_manager.class.php';
require_once Path :: get(SYS_APP_PATH) . 'lib/personal_calendar/personal_calendar_publication.class.php';

/**
 * Class that represents the personal agenda data from dokeos 1.8.5
 * @author Sven Vanpoucke
 */
class Dokeos185PersonalAgenda extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'personal_agenda';
    const DATABASE_NAME = 'user_personal_database';

    /**
     * personal agenda properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_USER = 'user';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_TEXT = 'text';
    const PROPERTY_DATE = 'date';
    const PROPERTY_ENDDATE = 'enddate';
    const PROPERTY_COURSE = 'course';

    /**
     * Get the default properties of all personal agenda.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER, self :: PROPERTY_TITLE, self :: PROPERTY_TEXT, self :: PROPERTY_DATE, self :: PROPERTY_ENDDATE, self :: PROPERTY_COURSE);
    }

    /**
     * Returns the id of this personal agenda.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the user of this personal agenda
     * @return int The user ID
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Returns the date of this personal agenda
     * @return int The date
     */
    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    /**
     * Returns the end date of this personal agenda
     * @return int The end date
     */
    function get_enddate()
    {
        return $this->get_default_property(self :: PROPERTY_ENDDATE);
    }

    /**
     * Returns the course of this personal agenda
     * @return int The course ID
     */
    function get_course()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE);
    }

    /**
     * Returns the title of this personal agenda
     * @return string The title
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the text of this personal agenda
     * @return string The text
     */
    function get_text()
    {
        return $this->get_default_property(self :: PROPERTY_TEXT);
    }

    /**
     * Check if the personal agenda is valid
     * @return true if the personal agenda is valid
     */
    function is_valid()
    {
        if (!$this->get_user() || (!$this->get_title() && trim(strip_tags($this->get_text())) == '') ||
                (!$this->get_title() && !$this->get_text()) || !$this->get_date() ||
                $this->get_failed_element($this->get_user(), 'main_database_.user')) {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'class', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new personal agenda
     * @return the new personal agenda
     */
    function convert_data()
    {
        $new_start_date = strtotime($this->get_date());
        $new_end_date = strtotime($this->get_enddate());

        // Create calendar event
        $chamilo_calendar_event = new CalendarEvent();
        $chamilo_calendar_event->set_start_date($new_start_date);

        if (!$this->get_enddate()) {
            $chamilo_calendar_event->set_end_date($new_start_date);
        } else {
            $chamilo_calendar_event->set_end_date($new_end_date);
        }

        if (!$this->get_title()) {
            $chamilo_calendar_event->set_title(Utilities :: truncate_string($this->get_text(), 50, true));
        } else {
            $chamilo_calendar_event->set_title($this->get_title());
        }

        if (!$this->get_text()) {
            $chamilo_calendar_event->set_description($this->get_title());
        } else {
            $chamilo_calendar_event->set_description($this->get_text());
        }

        //Get owner_ID from
        $owner_id = $this->get_id_reference($this->get_user(), 'main_database.user');
        if ($owner_id) {
            $chamilo_calendar_event->set_owner_id($owner_id);
        }


        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($owner_id, Translation :: get('CalendarEvents'));
        $chamilo_calendar_event->set_parent_id($chamilo_category_id);
        $chamilo_calendar_event->create();

        //control if the personal_agenda application exists
        $is_registered = AdminDataManager :: is_registered('personal_calendar');

        if ($is_registered) {
            //Create personal agenda publication
            $chamilo_personal_calendar = new PersonalCalendarPublication();
            $chamilo_personal_calendar->set_content_object_id($chamilo_calendar_event->get_id());
            $chamilo_personal_calendar->set_publisher($owner_id);
            $chamilo_personal_calendar->set_published($new_start_date);
            $chamilo_personal_calendar->create_all();

            return $chamilo_calendar_event;
        }
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    function get_database_name()
    {
        return self :: DATABASE_NAME;
    }

}
?>