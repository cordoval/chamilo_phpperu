<?php

/**
 * $Id: dokeos185_group.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group (table group_info)
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Group extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'group_info';

    /**
     * Group properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_MAX_STUDENT = 'max_student';
    const PROPERTY_DOC_STATE = 'doc_state';
    const PROPERTY_CALENDAR_STATE = 'calendar_state';
    const PROPERTY_WORK_STATE = 'work_state';
    const PROPERTY_ANNOUNCEMENTS_STATE = 'groups_state';
    const PROPERTY_SECRET_DIRECTORY = 'secret_directory';
    const PROPERTY_self_REGISTRATION_ALLOWED = 'self_registration_allowed';
    const PROPERTY_self_UNREGISTRATION_ALLOWED = 'self_unregistration_allowed';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Announcement object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Group($defaultProperties = array())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_MAX_STUDENT, self :: PROPERTY_DOC_STATE, self :: PROPERTY_CALENDAR_STATE, self :: PROPERTY_WORK_STATE, self :: PROPERTY_ANNOUNCEMENTS_STATE, self :: PROPERTY_SECRET_DIRECTORY, self :: PROPERTY_self_REGISTRATION_ALLOWED, self :: PROPERTY_self_UNREGISTRATION_ALLOWED);
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
     * Returns the category_id of this group.
     * @return string the category_id.
     */
    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
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
     * Returns the calendar_state of this announcement.
     * @return int The calendar_state.
     */
    function get_calendar_state()
    {
        return $this->get_default_property(self :: PROPERTY_CALENDAR_STATE);
    }

    /**
     * Returns the work_state of this announcement.
     * @return string the work_state.
     */
    function get_work_state()
    {
        return $this->get_default_property(self :: PROPERTY_WORK_STATE);
    }

    /**
     * Returns the announcements_state of this announcement.
     * @return string the announcements_state.
     */
    function get_announcements_state()
    {
        return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE);
    }

    /**
     * Returns the secret_directory of this announcement.
     * @return date the secret_directory.
     */
    function get_secret_directory()
    {
        return $this->get_default_property(self :: PROPERTY_SECRET_DIRECTORY);
    }

    /**
     * Returns the self_registration_allowed of this announcement.
     * @return int the self_registration_allowed.
     */
    function get_self_registration_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_self_REGISTRATION_ALLOWED);
    }

    /**
     * Returns the self_unregistration_allowed of this announcement.
     * @return int the self_unregistration_allowed.
     */
    function get_self_unregistration_allowed()
    {
        return $this->get_default_property(self :: PROPERTY_self_UNREGISTRATION_ALLOWED);
    }

    /**
     * Check if the group is valid
     * @param Course $course the course
     * @return true if the group is valid 
     */
    function is_valid()
    {

        if (!$this->get_name() || $this->get_self_registration_allowed() == NULL || $this->get_self_unregistration_allowed() == NULL) {
            $this->create_failed_element($this->get_id());
            return false;
        }

        return true;
    }

    /**
     * Convert to new group
     * @param Course $course the course
     * @return the new group
     */
    function convert_data()
    {
        $course = $this->get_course();
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        $chamilo_lcms_group = new CourseGroup();
        $chamilo_lcms_group->set_course_code($new_course_code);
        $chamilo_lcms_group->set_name($this->get_name());
        $chamilo_lcms_group->set_max_number_of_members($this->get_max_student());

        if ($this->get_description()) {
            $chamilo_lcms_group->set_description($this->get_description());
        } else {
            $chamilo_lcms_group->set_description($this->get_name());
        }

        $chamilo_lcms_group->set_self_registration_allowed($this->get_self_registration_allowed());
        $chamilo_lcms_group->set_self_unregistration_allowed($this->get_self_unregistration_allowed());
        $chamilo_lcms_group->create();

        $this->create_id_reference($this->get_id(), $chamilo_lcms_group->get_id());

        return $chamilo_lcms_group;
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

}
?>