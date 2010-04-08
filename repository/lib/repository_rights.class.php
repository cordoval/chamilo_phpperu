<?php
/**
 * $Id: repository_rights.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
class RepositoryRights
{
    const ADD_RIGHT = '1';
    const EDIT_RIGHT = '2';
    const DELETE_RIGHT = '3';
    const SEARCH_RIGHT = '4';
    const VIEW_RIGHT = '5';
    const USE_RIGHT = '6';
    const REUSE_RIGHT = '7';

    function get_available_rights()
    {
        $reflect = new ReflectionClass('RepositoryRights');
        return $reflect->getConstants();
    }

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, RepositoryManager :: APPLICATION_NAME);
    }

    function get_location_by_identifier($type, $identifier, $tree_identifier = '0', $tree_type = 'root')
    {
        return RightsUtilities :: get_location_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, $tree_type);
    }

    function get_location_id_by_identifier($type, $identifier, $tree_identifier = '0', $tree_type = 'root')
    {
        return RightsUtilities :: get_location_id_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, $tree_type);
    }

    function get_root_id()
    {
        return RightsUtilities :: get_root_id(RepositoryManager :: APPLICATION_NAME);
    }

    function get_root()
    {
        return RightsUtilities :: get_root(RepositoryManager :: APPLICATION_NAME);
    }

    function create_user_root($user)
    {
        return RightsUtilities :: create_subtree_root_location(RepositoryManager :: APPLICATION_NAME, $user->get_id(), 'user_tree');
    }

	static function create_location_in_user_tree($name, $type, $identifier, $parent, $user_id)
    {
    	return RightsUtilities :: create_location($name, RepositoryManager :: APPLICATION_NAME, $type, $identifier, 0, $parent, 0, $user_id, 'user_tree');
    }

    function get_user_root_id($user_id)
    {
        return RightsUtilities :: get_root_id(RepositoryManager :: APPLICATION_NAME, 'user_tree', $user_id);
    }

	static function get_location_id_by_identifier_from_user_subtree($type, $identifier, $user_id)
    {
    	return RightsUtilities :: get_location_id_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier, $user_id, 'user_tree');
    }

	static function is_allowed_in_user_subtree($right, $location, $type, $user_id)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, RepositoryManager :: APPLICATION_NAME, null, $user_id, 'user_tree');
    }
}
?>