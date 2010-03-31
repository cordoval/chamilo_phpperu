<?php

/**
 * $Id: dokeos185_personal_agenda.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_personal_agenda.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/calendar_event/calendar_event.class.php';
require_once Path :: get(SYS_APP_PATH) . 'lib/personal_calendar/personal_calendar_event.class.php';
require_once Path :: get(SYS_APP_PATH) . 'lib/personal_calendar/calendar_event_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * Class that represents the personal agenda data from dokeos 1.8.5
 * @author Sven Vanpoucke
 */
class Dokeos185PersonalAgenda extends ImportPersonalAgenda
{
    /**
     ** Migration data manager
     */
    private static $mgdm;
    
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
     * Default properties of the personal agenda object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new personal agenda object
     * @param array $defaultProperties The default properties of the personal agenda
     *                                 object. Associative array.
     */
    function Dokeos185PersonalAgenda($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this personal agenda object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this personal agenda.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all personal agenda.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER, self :: PROPERTY_TITLE, self :: PROPERTY_TEXT, self :: PROPERTY_DATE, self :: PROPERTY_ENDDATE, self :: PROPERTY_COURSE);
    }

    /**
     * Sets a default property of this personal agenda by name.
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
     * Checks if the given identifier is the name of a default personal agenda
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
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
    function is_valid($parameters)
    {
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_user() ||(! $this->get_title() && trim(strip_tags($this->get_text())) == '') || (! $this->get_title() && ! $this->get_text()) || ! $this->get_date() || $mgdm->get_failed_element('dokeos_main.user', $this->get_user()) || ! $mgdm->get_id_reference($this->get_user(), 'user_user'))
        {
            $mgdm->add_failed_element($this->get_id(), 'dokeos_user.personal_agenda');
            return false;
        }
        return true;
    }

    /**
     * Convert to new personal agenda
     * @return the new personal agenda
     */
    function convert_to_lcms($parameters)
    {
    	//control if the personal_agenda application exists
		$is_registered = AdminDataManager :: get_instance()->is_registered('personal_agenda');
        // Convert profile fields to Profile object if the user has user profile data
        if ($is_registered)
        {
        	$mgdm = MigrationDataManager :: get_instance();
        	// Create calendar event	
        	$lcms_calendar_event = new CalendarEvent();
        	$lcms_calendar_event->set_start_date($mgdm->make_unix_time($this->get_date()));
        
        	if (! $this->get_enddate())
            	$lcms_calendar_event->set_end_date($mgdm->make_unix_time($this->get_date()));
        	else
            	$lcms_calendar_event->set_end_date($mgdm->make_unix_time($this->get_enddate()));
        
        	if (! $this->get_title())
            	$lcms_calendar_event->set_title(substr(strip_tags($this->get_text()), 0, 20));
        	else
            	$lcms_calendar_event->set_title($this->get_title());
        
        	if (! $this->get_text())
            	$lcms_calendar_event->set_description($this->get_title());
        	else
            	$lcms_calendar_event->set_description($this->get_text());
            
        	//Get owner_ID from
        	$owner_id = $mgdm->get_id_reference($this->get_user(), 'user_user');
        	if ($owner_id)
            	$lcms_calendar_event->set_owner_id($owner_id);
            
            
        	$lcms_category_id = $mgdm->get_repository_category_by_name($owner_id,Translation :: get('CalendarEvents'));    
        	$lcms_calendar_event->set_parent_id($lcms_category_id);
        	$lcms_calendar_event->create();
        
        	//Create personal agenda publication
        	$lcms_personal_calendar = new CalendarEventPublication();
        	$lcms_personal_calendar->set_calendar_event($lcms_calendar_event->get_id());
        	$lcms_personal_calendar->set_publisher($owner_id);
        	$lcms_personal_calendar->set_published($mgdm->make_unix_time($this->get_date()));
        	$lcms_personal_calendar->create_all();
        
        	return $lcms_calendar_event;
        }
    }

    /**
     * Retrieve all personal agendas from the database
     * @param array $parameters parameters for the retrieval
     * @return array of personal agendas
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'user_personal_database';
        $tablename = 'personal_agenda';
        $classname = 'Dokeos185PersonalAgenda';
        
        return $old_mgdm->get_all($db, $tablename, $classname, null, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'user_personal_database';
        $array['table'] = 'personal_agenda';
        return $array;
    }
}
?>
