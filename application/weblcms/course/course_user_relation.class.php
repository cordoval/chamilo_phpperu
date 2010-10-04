<?php
/**
 * $Id: course_user_relation.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

/**
 *	This class represents a course user relation in the weblcms.
 *
 *	course user relations have a number of default properties:
 *	- course code: the code of the course;
 *	- user_id: the user's id;
 *	- status: the subscription status (teacher or student);
 *	- role: the user's role;
 *	- course_group_id: the course_group id;
 *  - tutor_id: the id of the tutor;
 *	- sort: the sort order;
 *	- category: the category in which the user has placed the course;
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 */

class CourseUserRelation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_COURSE = 'course_id';
    const PROPERTY_USER = 'user_id';
    const PROPERTY_STATUS = 'status';

    /**
     * Get the default properties of all course user relations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_COURSE, self :: PROPERTY_USER, self :: PROPERTY_STATUS);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the course of this course user relation object
     * @return int
     */
    function get_course()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE);
    }

    /**
     * Sets the course of this course user relation object
     * @param int $course
     */
    function set_course($course)
    {
        $this->set_default_property(self :: PROPERTY_COURSE, $course);
    }

    /**
     * Returns the user of this course user relation object
     * @return int
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this course user relation object
     * @param int $user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    /**
     * Gets the user
     * @return User
     * @todo The functions get_user and set_user should work with a User object
     * and not with the user id's!
     */
    function get_user_object()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_user());
    }

    /**
     * Returns the status of this course user relation object
     * @return int
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this course user relation object
     * @param int $status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>