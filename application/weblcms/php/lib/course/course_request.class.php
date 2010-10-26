<?php
namespace application\weblcms;

use common\libraries\Path;

/**
 * $Id: course_request.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course
 */

class CourseRequest extends CommonRequest
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_COURSE_ID = 'course_id';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_COURSE_ID));
    }

    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    function set_course_id($course_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>