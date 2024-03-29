<?php
namespace application\weblcms;

use group\GroupDataManager;
use common\libraries\Utilities;
use common\libraries\DataClass;

/**
 * $Id: course_group_relation.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */

/**
 * This class represents a course group relation in the weblcms.
 *
 * course group relations have a number of default properties:
 * - course_id: the id of the course;
 * - group_id: the group's id;
 *
 */

class CourseGroupRelation extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_GROUP_ID = 'group_id';
    const PROPERTY_STATUS = 'status';

    const STATUS_TEACHER = 1;
    const STATUS_STUDENT = 5;

    /**
     * Get the default properties of all course group relations.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
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
     * Returns the status of this course group relation object
     * @return int
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this course group relation object
     * @param int $status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
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

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>