<?php
/**
 * $Id: forum_publication_category.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.category_manager
 */
require_once Path :: get_common_extensions_path() . 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../forum_data_manager.class.php';

/**
 *	@author Sven Vanpoucke
 */

class ForumPublicationCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    function create()
    {
        $fdm = ForumDataManager :: get_instance();

        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $this->get_parent());
        $sort = $fdm->retrieve_max_sort_value(self :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        $this->set_display_order($sort + 1);

        $succes = $fdm->create_forum_publication_category($this);
        if(!$succes)
        {
        	return false;
        }
        
        $parent = $this->get_parent();
        if ($parent == 0)
        {
            $parent_id = ForumRights :: get_forums_subtree_root_id();
        }
        else
        {
            $parent_id = ForumRights :: get_location_id_by_identifier_from_forums_subtree($this->get_parent()); 
        }
        
    	return ForumRights :: create_location_in_forums_subtree($this->get_name(), $this->get_id(), $parent_id);
    }

    function update($move = false)
    {
        $succes = ForumDataManager :: get_instance()->update_forum_publication_category($this);
    	if(!$succes)
        {
        	return false;
        }
        
        if($move)
        {
        	if($this->get_parent())
        	{
        		$new_parent_id = ForumRights :: get_location_id_by_identifier_from_forums_subtree($this->get_parent());
        	}
        	else
        	{
        		$new_parent_id = ForumRights :: get_forums_subtree_root_id();	
        	}
        	
        	$location = ForumRights :: get_location_by_identifier_from_forums_subtree($this->get_id());
        	return $location->move($new_parent_id);
        }
        
        return true;
    }

    function delete()
    {
    	$location = ForumRights :: get_location_by_identifier_from_forums_subtree($this->get_id());
		if($location)
		{
			if(!$location->remove())
			{
				return false;
			}
		}
		
    	return ForumDataManager :: get_instance()->delete_forum_publication_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}