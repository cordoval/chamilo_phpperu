<?php
/**
 * $Id: content_object_publication_category.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.category_manager
 */
/**
 *	@author Sven Vanpoucke
 */

require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

class ContentObjectPublicationCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE = 'course_id';
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_ALLOW_CHANGE = 'allow_change';

    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();

        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $this->get_parent());
        $sort = $wdm->retrieve_max_sort_value(self :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        $this->set_display_order($sort + 1);

        return $wdm->create_content_object_publication_category($this);
    }

    function create_dropbox($course_code)
    {
        $this->set_course($course_code);
        $this->set_tool('document');
        $this->set_name(Translation :: get('Dropbox'));
        $this->set_parent(0);
        $this->set_allow_change(0);

        $this->create();
    }

    function update()
    {
        return WeblcmsDataManager :: get_instance()->update_content_object_publication_category($this);
    }

    function delete()
    {
        return WeblcmsDataManager :: get_instance()->delete_content_object_publication_category($this);
    }

    static function get_default_property_names()
    {
        return array(self :: PROPERTY_COURSE, self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_TOOL, self :: PROPERTY_PARENT, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_ALLOW_CHANGE);
    }

    function get_course()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE);
    }

    function set_course($course)
    {
        $this->set_default_property(self :: PROPERTY_COURSE, $course);
    }

    function get_tool()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL);
    }

    function set_tool($tool)
    {
        $this->set_default_property(self :: PROPERTY_TOOL, $tool);
    }

    function get_allow_change()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_CHANGE);
    }

    function set_allow_change($allow_change)
    {
        $this->set_default_property(self :: PROPERTY_ALLOW_CHANGE, $allow_change);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}