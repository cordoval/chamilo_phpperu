<?php
namespace application\weblcms;

use common\libraries\Utilities;
use common\libraries\Path;

/**
 * $Id: course_request.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course
 */

class CourseCreateRequest extends CommonRequest
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_NAME = 'course_name';
    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_COURSE_NAME, self :: PROPERTY_COURSE_TYPE_ID));
    }

    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_course_name()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_NAME);
    }

    function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    function set_course_name($course_name)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_NAME, $course_name);
    }

    function set_course_type_id($course_type_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
        //return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>