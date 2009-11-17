<?php
/**
 * $Id: profiler_category.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.category_manager
 */
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../profiler_data_manager.class.php';

/**
 *	@author Sven Vanpoucke
 */

class ProfilerCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'category';

    function create()
    {
        $wdm = ProfilerDataManager :: get_instance();
        $this->set_id($wdm->get_next_category_id());
        $this->set_display_order($wdm->select_next_category_display_order($this->get_parent()));
        return $wdm->create_category($this);
    }

    function update()
    {
        return ProfilerDataManager :: get_instance()->update_category($this);
    }

    function delete()
    {
        return ProfilerDataManager :: get_instance()->delete_category($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}