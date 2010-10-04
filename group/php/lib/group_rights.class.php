<?php

/**
 * $Id: laika_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

require_once Path :: get_group_path() . 'lib/group_manager/group_manager.class.php';

class GroupRights extends RightsUtilities
{
    const RIGHT_VIEW = 1;
    const RIGHT_CREATE = 2;
    const RIGHT_EDIT = 3;
    const RIGHT_DELETE = 4;
    const RIGHT_EXPORT = 5;
    const RIGHT_MOVE = 6;
    const RIGHT_SUBSCRIBE = 7;
    const RIGHT_UNSUBSCRIBE = 8;
    const RIGHT_EDIT_RIGHTS = 9;

    const TYPE_ROOT = 0;
    const TYPE_GROUP = 1;

    static function get_available_rights()
    {
        return parent :: get_available_rights(GroupManager :: APPLICATION_NAME);
    }

    static function get_available_types()
    {
        return parent :: get_available_types(GroupManager :: APPLICATION_NAME);
    }

    static function is_allowed($right, $location)
    {
        return RightsUtilities :: is_allowed($right, $location, self :: TYPE_GROUP, GroupManager :: APPLICATION_NAME);
    }

    static function create_location_in_groups_subtree($name, $identifier, $parent, $tree_identifier = 0)
    {
        return RightsUtilities :: create_location($name, GroupManager :: APPLICATION_NAME, self :: TYPE_GROUP, $identifier, 1, $parent, 0, $tree_identifier, self :: TYPE_ROOT);
    }

    static function get_groups_subtree_root($tree_identifier = 0)
    {
        return RightsUtilities :: get_root(GroupManager :: APPLICATION_NAME, self :: TYPE_ROOT, $tree_identifier);
    }

    static function get_groups_subtree_root_id($tree_identifier = 0)
    {
        return RightsUtilities :: get_root_id(GroupManager :: APPLICATION_NAME, self :: TYPE_ROOT, $tree_identifier);
    }

    static function get_location_id_by_identifier_from_groups_subtree($identifier, $tree_identifier = 0)
    {
        return RightsUtilities :: get_location_id_by_identifier(GroupManager :: APPLICATION_NAME, self :: TYPE_GROUP, $identifier, $tree_identifier, self :: TYPE_ROOT);
    }

    static function is_allowed_in_groups_subtree($right, $location, $tree_identifier = 0)
    {
        return RightsUtilities :: is_allowed($right, $location, self :: TYPE_GROUP, GroupManager :: APPLICATION_NAME, null, $tree_identifier, self :: TYPE_ROOT);
    }

    static function get_location_by_identifier_from_groups_subtree($identifier, $tree_identifier = 0)
    {
        return RightsUtilities :: get_location_by_identifier(GroupManager :: APPLICATION_NAME, self :: TYPE_GROUP, $identifier, $tree_identifier, self :: TYPE_ROOT);
    }
}
?>