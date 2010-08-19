<?php

/**
 * $Id: laika_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */


require_once Path :: get_group_path() . 'lib/group_manager/group_manager.class.php';

class GroupRights
{
    const VIEW_RIGHT = '1';
    const CREATE_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';
    const EXPORT_RIGHT = '5';
    const MOVE_RIGHT = '6';
    const SUBSCRIBE_RIGHT = '7';
    const UNSUBSCRIBE_RIGHT = '8';
    const EDIT_RIGHTS_RIGHT = '9';

    const TREE_TYPE_GROUP = '1';
    const TYPE_GROUP = '1';
   

    function get_available_rights()
    {
        $reflect = new ReflectionClass('GroupRights');

	    $rights = $reflect->getConstants();

	    foreach($rights as $key => $right)
		{
			if(substr(strtolower($key), 0, 8) == 'location')
			{
				unset($rights[$key]);
			}
		}

	    return $rights;
    }

    function is_allowed($right, $location)
    {
        return RightsUtilities :: is_allowed($right, $location, self::TYPE_GROUP, GroupManager :: APPLICATION_NAME);
    }

    static function create_location_in_groups_subtree($name, $identifier, $parent, $tree_identifier = 0)
   {
       return RightsUtilities :: create_location($name, GroupManager :: APPLICATION_NAME, self::TYPE_GROUP, $identifier, 1, $parent, 0, $tree_identifier, self::TREE_TYPE_GROUP);
   }

   static function get_groups_subtree_root($tree_identifier = 0)
   {
       return RightsUtilities :: get_root(GroupManager :: APPLICATION_NAME, self::TREE_TYPE_GROUP, $tree_identifier);
   }

   static function get_groups_subtree_root_id($tree_identifier = 0)
   {
       return RightsUtilities :: get_root_id(GroupManager :: APPLICATION_NAME, self::TREE_TYPE_GROUP, $tree_identifier);
   }

   static function get_location_id_by_identifier_from_groups_subtree( $identifier, $tree_identifier = 0)
   {
       return RightsUtilities :: get_location_id_by_identifier(GroupManager :: APPLICATION_NAME, self::TYPE_GROUP, $identifier, $tree_identifier, self::TREE_TYPE_GROUP);
   }

   static function is_allowed_in_groups_subtree($right, $location, $tree_identifier = 0)
   {
        return RightsUtilities :: is_allowed($right, $location, self::TYPE_GROUP, GroupManager :: APPLICATION_NAME, null, $tree_identifier, self::TREE_TYPE_GROUP);
   }

   static function get_location_by_identifier_from_groups_subtree($identifier, $tree_identifier = 0)
   {
       return RightsUtilities :: get_location_by_identifier(GroupManager :: APPLICATION_NAME, self :: TYPE_GROUP, $identifier, $tree_identifier, self :: TREE_TYPE_GROUP);
   }
}
?>