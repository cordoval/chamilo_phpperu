<?php
/**
 * $Id: course_group_relation.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

/**
 *	This class represents a course group relation in the weblcms.
 *
 *	course group relations have a number of default properties:
 *	- course_id: the id of the course;
 *	- group_id: the group's id;
 *
 */

class CourseGroupRelation extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties of all course group relations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_COURSE_ID, self :: PROPERTY_GROUP_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the course of this course group relation object
     * @return int
     */
    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    /**
     * Sets the course of this course group relation object
     * @param int $course
     */
    function set_course_id($course_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * Returns the group_id of this course group relation object
     * @return int
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this course group relation object
     * @param int $group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    /**
     * Gets the group
     * @return Group
     */
    function get_group_object()
    {
        $gdm = GroupDataManager :: get_instance();
        return $gdm->retrieve_group($this->get_group_id());
    }

    /**
     * Creates the course group relation object in persistent storage
     * @return boolean
     */
    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $success = $wdm->create_course_group_relation($this);

        if (! $success)
        {
            return false;
        }
        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>