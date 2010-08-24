<?php

/**
 * $Id: user_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package user
 */


require_once Path :: get_user_path() . 'lib/user_manager/user_manager.class.php';

class UserRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';
	
    const LOCATION_APPROVER_BROWSER = 1;
    const LOCATION_APPROVER = 2;
    const LOCATION_FIELDS_BUILDER = 3;
    
    const TREE_TYPE_USER = 1;
    const TYPE_USER = 1;
    
    const TYPE_COMPONENT = 1;
    
    function get_available_rights()
    {
        $reflect = new ReflectionClass('UserRights');

	    $rights = $reflect->getConstants();

	    foreach($rights as $key => $right)
		{
			if(substr(strtolower($key), -5) != 'right')
			{
				unset($rights[$key]);
			}
		}

	    return $rights;
    }

    static function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, UserManager :: APPLICATION_NAME);
    }
    
	static function create_location_in_users_subtree($name, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, UserManager :: APPLICATION_NAME, self :: TYPE_USER, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_USER);
    }
    
    static function get_users_subtree_root()
    {
    	return RightsUtilities :: get_root(UserManager :: APPLICATION_NAME, self :: TREE_TYPE_USER, 0);
    }
    
	static function get_users_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(UserManager :: APPLICATION_NAME, self :: TREE_TYPE_USER, 0);
    }
    
    static function get_location_id_by_identifier_from_users_subtree($identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(UserManager :: APPLICATION_NAME, self :: TYPE_USER, $identifier, 0, self :: TREE_TYPE_USER);
    }
    
	static function get_location_by_identifier_from_users_subtree($identifier)
    {
    	return RightsUtilities :: get_location_by_identifier(UserManager :: APPLICATION_NAME, self :: TYPE_USER, $identifier, 0, self :: TREE_TYPE_USER);
    }
    
	static function is_allowed_in_users_subtree($right, $location)
    {
    	 return RightsUtilities :: is_allowed($right, $location, self :: TYPE_USER, UserManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_USER);
    }
    
    static function create_user_subtree_root_location()
    {
    	return RightsUtilities :: create_location('user_tree', UserManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_USER);
    }
}
?>