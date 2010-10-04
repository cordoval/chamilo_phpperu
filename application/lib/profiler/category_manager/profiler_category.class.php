<?php

/**
 * $Id: profiler_category.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.category_manager
 */
require_once Path :: get_common_extensions_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../profiler_data_manager.class.php';
require_once dirname(__FILE__) . '/../profiler_rights.class.php';


/**
 * 	@author Sven Vanpoucke
 */
class ProfilerCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'category';

    function create()
    {
        $wdm = ProfilerDataManager :: get_instance();
        $this->set_display_order($wdm->select_next_category_display_order($this->get_parent()));
        if (!$wdm->create_category($this))
        {
            return false;
        }
        else
        {
            $parent = $this->get_parent();

            if ($parent == 0)
            {
                $parent_id = ProfilerRights :: get_profiler_subtree_root_id();
            }
            else
            {
                $parent_id = ProfilerRights :: get_location_id_by_identifier_from_profiler_subtree($parent, ProfilerRights :: TYPE_CATEGORY);
            }

            return ProfilerRights :: create_location_in_profiler_subtree($this->get_name(), $this->get_id(), $parent_id, ProfilerRights :: TYPE_CATEGORY);
        }
    }

    function update()
    {
        if (!ProfilerDataManager :: get_instance()->update_category($this))
        {
            return false;
        }
        else
        {
            $new_parent = $this->get_parent();

            if ($parent == 0)
            {
                $parent_id = ProfilerRights :: get_profiler_subtree_root_id();
            }
            else
            {
                $parent_id = ProfilerRights :: get_location_id_by_identifier_from_profiler_subtree($parent, ProfilerRights :: TYPE_CATEGORY);
            }

            return ProfilerRights :: create_location_in_profiler_subtree($this->get_name(), $this->get_id(), $parent_id, ProfilerRights :: TYPE_CATEGORY);
        }
    }

    function delete()
    {
        $location = ProfilerRights :: get_location_by_identifier_from_profiler_subtree($this->get_id(), AssessmentRights :: TYPE_CATEGORY);
    	if($location)
    	{
    		if(!$location->remove())
    		{
    			return false;
    		}
    	}
        return parent::delete();
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

}