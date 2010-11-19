<?php
namespace application\weblcms;

use common\libraries\Utilities;
use common\libraries\DataClass;

/**
 * $Id: course_group_subscribe_right.class.php 216 2009-11-13 14:08:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course
 */
/**
 * This class represents a course_rights for a course in the weblcms.
 *
 * To access the values of the properties, this class and its subclasses should
 * provide accessor methods. The names of the properties should be defined as
 * class constants, for standardization purposes. It is recommended that the
 * names of these constants start with the string "PROPERTY_".
 *
 */
class CourseGroupSubscribeRight extends DataClass
{

    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_ID = "course_id";
    const PROPERTY_GROUP_ID = "group_id";
    const PROPERTY_SUBSCRIBE = "subscribe";

    const SUBSCRIBE_NONE = 0;
    const SUBSCRIBE_DIRECT = 3;
    const SUBSCRIBE_REQUEST = 2;
    const SUBSCRIBE_CODE = 1;
    const UNSUBSCRIBE = 4;

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        if (empty($extended_property_names))
            $extended_property_names = array(self :: PROPERTY_COURSE_ID);
        return array_merge($extended_property_names, array(self :: PROPERTY_GROUP_ID, self :: PROPERTY_SUBSCRIBE));
    }

    /*
     * Getters
     */

    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    function get_subscribe()
    {
        return $this->get_default_property(self :: PROPERTY_SUBSCRIBE);
    }

    /*
     * Setters
     */

    function set_course_id($course_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function set_subscribe($subscribe)
    {
        $this->set_default_property(self :: PROPERTY_SUBSCRIBE, $subscribe);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    static function convert_course_type_right_to_course_right($course_type_right, $course_id)
    {
        $course_right = new CourseGroupSubscribeRight();
        $course_right->set_course_id($course_id);
        $course_right->set_group_id($course_type_right->get_group_id());
        $course_right->set_subscribe($course_type_right->get_subscribe());
        return $course_right;
    }
}
