<?php
/**
 * $Id: course_section.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

class CourseSection extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const TYPE_DISABLED = '0';
    const TYPE_TOOL = '1';
    const TYPE_LINK = '2';
    const TYPE_ADMIN = '3';
    
    const PROPERTY_COURSE_CODE = 'course_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_VISIBLE = 'visible';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_COURSE_CODE, self :: PROPERTY_NAME, self :: PROPERTY_TYPE, self :: PROPERTY_VISIBLE, self :: PROPERTY_DISPLAY_ORDER));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    function set_course_code($course_code)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function get_visible()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE);
    }

    function set_visible($visible)
    {
        $this->set_default_property(self :: PROPERTY_VISIBLE, $visible);
    }

    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $this->set_id($wdm->get_next_course_section_id());
        
        $condition = new EqualityCondition(self :: PROPERTY_COURSE_CODE, $this->get_course_code());
        $sort = $wdm->retrieve_max_sort_value(self :: get_table_name(), self :: PROPERTY_DISPLAY_ORDER, $condition);
        $this->set_display_order($sort + 1);
        
        return $wdm->create_course_section($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
