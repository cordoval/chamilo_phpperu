<?php
/**
 * $Id: repository_category.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.category_manager
 */
/**
 *	@author Sven Vanpoucke
 */
require_once Path :: get_application_library_path() . 'category_manager/platform_category.class.php';


class RepositoryCategory extends PlatformCategory
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';

    function create()
    {
        if (! $this->get_user_id())
        {
            $user_id = Session :: get_user_id();
            if ($user_id)
            {
                $this->set_user_id($user_id);
            }
        }
        else
            $user_id = $this->get_user_id();

        $rdm = RepositoryDataManager :: get_instance();
        $this->set_display_order($rdm->select_next_category_display_order($this->get_parent(), $user_id));
        if (! $rdm->create_category($this))
        {
            return false;
        }

        $parent = $this->get_parent();
        if ($parent == 0)
        {
            $parent_id = RepositoryRights :: get_user_root_id($user_id);
        }
        else
        {
            $parent_id = RepositoryRights :: get_location_id_by_identifier_from_user_subtree(RepositoryRights :: TYPE_USER_CATEGORY, $this->get_parent(), $user_id); 
        }
        
    	if (!RepositoryRights :: create_location_in_user_tree($this->get_name(), RepositoryRights :: TYPE_USER_CATEGORY, $this->get_id(), $parent_id, $user_id))
        {
            return false;
        }

        return true;
    }

    function update($move = false)
    {
        if(!RepositoryDataManager :: get_instance()->update_category($this))
        {
        	return false;
        }     
        
    	if($move)
        {
        	if($this->get_parent())
        	{
        		$new_parent_id = RepositoryRights :: get_location_id_by_identifier_from_user_subtree(RepositoryRights :: TYPE_USER_CATEGORY, $this->get_parent(), $this->get_user_id());
        	}
        	else
        	{
        		$new_parent_id = RepositoryRights :: get_user_root_id();	
        	}
        	
        	$location =  RepositoryRights :: get_location_by_identifier_from_users_subtree(RepositoryRights :: TYPE_USER_CATEGORY, $this->get_id(), $this->get_user_id());
        	return $location->move($new_parent_id);
        }
        
    	return true; 
    }

    function delete()
    {
    	$location = RepositoryRights :: get_location_by_identifier_from_users_subtree(RepositoryRights :: TYPE_USER_CATEGORY, $this->get_id(), $this->get_user_id());
		if($location)
		{
			if(!$location->remove())
			{
				return false;
			}
		}
		
    	return RepositoryDataManager :: get_instance()->delete_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_PARENT, self :: PROPERTY_DISPLAY_ORDER);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
}