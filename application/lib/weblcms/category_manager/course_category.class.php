<?php
/**
 * $Id: course_category.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.category_manager
 */
/**
 *	@author Sven Vanpoucke
 */
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

class CourseCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();

        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $this->get_parent());
        $sort = $wdm->retrieve_max_sort_value(self :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        $this->set_display_order($sort + 1);

        if (! $wdm->create_category($this))
        {
            return false;
        }

        $location = new Location();
        $location->set_location($this->get_name());
        $location->set_application(WeblcmsManager :: APPLICATION_NAME);
        $location->set_type_from_object($this);
        $location->set_identifier($this->get_id());
        $location->set_parent(WeblcmsRights :: get_root_id());

        if (! $location->create())
        {
            return false;
        }

        return true;
    }

    function update()
    {
        return WeblcmsDataManager :: get_instance()->update_category($this);
    }

    function delete()
    {
        return WeblcmsDataManager :: get_instance()->delete_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}