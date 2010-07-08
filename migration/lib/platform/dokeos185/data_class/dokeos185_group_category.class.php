<?php

/**
 * $Id: dokeos185_group_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_group.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GroupCategory extends MigrationDataClass
{
    /**
     * Migration data manager
     */
    private static $mgdm;
    
    /**
     * Group properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'name';
    const PROPERTY_GROUPS_PER_USER = 'groups_per_user';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_MAX_STUDENT = 'max_student';
    const PROPERTY_DOC_STATE = 'doc_state';
    const PROPERTY_CALENDAR_STATE = 'calendar_state';
    const PROPERTY_WORK_STATE = 'work_state';
    const PROPERTY_ANNOUNCEMENTS_STATE = 'groups_state';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_self_REG_ALLOWED = 'self_reg_allowed';
    const PROPERTY_self_UNREG_ALLOWED = 'self_unreg_allowed';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Announcement object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Group($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_GROUPS_PER_USER, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_MAX_STUDENT, self :: PROPERTY_DOC_STATE, self :: PROPERTY_CALENDAR_STATE, self :: PROPERTY_WORK_STATE, self :: PROPERTY_ANNOUNCEMENTS_STATE, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_self_REG_ALLOWED, self :: PROPERTY_self_UNREG_ALLOWED);
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
     * Returns the id of this group.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the name of this group.
     * @return string the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the groups_per_user of this group.
     * @return string the groups_per_user.
     */
    function get_groups_per_user()
    {
        return $this->get_default_property(self :: PROPERTY_GROUPS_PER_USER);
    }

    /**
     * Returns the description of this group.
     * @return date the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the max_student of this group.
     * @return int the max_student.
     */
    function get_max_student()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_STUDENT);
    }

    /**
     * Returns the doc_state of this group.
     * @return int the doc_state.
     */
    function get_doc_state()
    {
        return $this->get_default_property(self :: PROPERTY_DOC_STATE);
    }

    /**
     * Returns the calendar_state of this groupcategory.
     * @return int The calendar_state.
     */
    function get_calendar_state()
    {
        return $this->get_default_property(self :: PROPERTY_CALENDAR_STATE);
    }

    /**
     * Returns the work_state of this groupcategory.
     * @return string the work_state.
     */
    function get_work_state()
    {
        return $this->get_default_property(self :: PROPERTY_WORK_STATE);
    }

    /**
     * Returns the groupcategorys_state of this groupcategory.
     * @return string the groupcategorys_state.
     */
    function get_groupcategorys_state()
    {
        return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE);
    }

    /**
     * Returns the display_order of this groupcategory.
     * @return date the display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the self_reg_allowed of this groupcategory.
     * @return int the self_reg_allowed.
     */
    function get_self_reg_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_self_REG_ALLOWED);
    }

    /**
     * Returns the self_unreg_allowed of this groupcategory.
     * @return int the self_unreg_allowed.
     */
    function get_self_unreg_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_self_UNREG_ALLOWED);
    }

    /**
     * Check if the group category is valid
     * @param Course $course the course
     * @return true if the group category is valid 
     */
    function is_valid_group_category($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new group category
     * @param Course $course the course
     * @return the new group category
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all group categories from the database
     * @param array $parameters parameters for the retrieval
     * @return array of group categories
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'group_category';
        $classname = 'Dokeos185GroupCategory';
        
        return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'group_category';
        return $array;
    }
    
	/**
	 * @param unknown_type $array
	 */
	function is_valid($array)
	{
		
	}

	/**
	 * @param unknown_type $array
	 */
	function convert_data
	{
		
	}

}
?>