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

    const TREE_TYPE_USER = 1;
    const TREE_TYPE_CONTENT_OBJECT = 2;
    const TREE_TYPE_EXTERNAL_REPOSITORY = 3;
    
    const TYPE_CONTENT_OBJECT = 1;
    const TYPE_USER_CATEGORY = 2;
    const TYPE_USER_CONTENT_OBJECT = 3;
    const TYPE_EXTERNAL_REPOSITORY = 4;
    
    function get_available_rights()
    {
        $reflect = new ReflectionClass('RepositoryRights');
        return $reflect->getConstants();
    }
    
    static function get_available_rights_for_users_subtree()
    {
    	return array('Search' => self :: SEARCH_RIGHT, 'View' => self :: VIEW_RIGHT, 
    				 'Use' => self :: USE_RIGHT, 'Reuse' => self :: REUSE_RIGHT);
    }
    
    static function get_available_rights_for_content_object_subtree()
    {
    	return array('View' => self :: VIEW_RIGHT, 'Add' => self :: ADD_RIGHT);
    }
    
    static function get_available_rights_for_external_repositories_substree()
    {
    	return array('Use' => self :: USE_RIGHT);
    }

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, RepositoryManager :: APPLICATION_NAME);
    }

    function get_location_by_identifier($type, $identifier, $tree_identifier = '0', $tree_type = 0)
    {
        return RightsUtilities :: get_location_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, $tree_type);
    }

    function get_location_id_by_identifier($type, $identifier, $tree_identifier = '0', $tree_type = 0)
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

    // User Subtree
    
    function create_user_root($user)
    {
        return RightsUtilities :: create_subtree_root_location(RepositoryManager :: APPLICATION_NAME, $user->get_id(), self :: TREE_TYPE_USER);
    }

	static function create_location_in_user_tree($name, $type, $identifier, $parent, $user_id)
    {
    	return RightsUtilities :: create_location($name, RepositoryManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, $user_id, self :: TREE_TYPE_USER);
    }

    static function get_user_root_id($user_id)
    {
        return RightsUtilities :: get_root_id(RepositoryManager :: APPLICATION_NAME, self :: TREE_TYPE_USER, $user_id);
    }
    
	static function get_user_root($user_id)
    {
        return RightsUtilities :: get_root(RepositoryManager :: APPLICATION_NAME, self :: TREE_TYPE_USER, $user_id);
    }
    
	static function get_location_id_by_identifier_from_user_subtree($type, $identifier, $user_id)
    {
    	return RightsUtilities :: get_location_id_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier, $user_id, self :: TREE_TYPE_USER);
    }
    
	static function get_location_by_identifier_from_users_subtree($type, $identifier, $user_id)
    {
    	return RightsUtilities :: get_location_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier, $user_id, self :: TREE_TYPE_USER);
    }

	static function is_allowed_in_user_subtree($right, $location, $type, $user_id)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, RepositoryManager :: APPLICATION_NAME, null, $user_id, self :: TREE_TYPE_USER);
    }
    
    // Content Object Type subtree
    
    static function create_location_in_content_objects_subtree($name, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, RepositoryManager :: APPLICATION_NAME, self :: TYPE_CONTENT_OBJECT, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_CONTENT_OBJECT);
    }
    
    static function get_content_objects_subtree_root()
    {
    	return RightsUtilities :: get_root(RepositoryManager :: APPLICATION_NAME, self :: TREE_TYPE_CONTENT_OBJECT, 0);
    }
    
	static function get_content_objects_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(RepositoryManager :: APPLICATION_NAME, self :: TREE_TYPE_CONTENT_OBJECT, 0);
    }
    
    static function get_location_id_by_identifier_from_content_objects_subtree($identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(RepositoryManager :: APPLICATION_NAME, self :: TYPE_CONTENT_OBJECT, $identifier, 0, self :: TREE_TYPE_CONTENT_OBJECT);
    }
    
	static function get_location_by_identifier_from_content_objects_subtree($identifier)
    {
    	return RightsUtilities :: get_location_by_identifier(RepositoryManager :: APPLICATION_NAME, self :: TYPE_CONTENT_OBJECT, $identifier, 0, self :: TREE_TYPE_CONTENT_OBJECT);
    }
    
	static function is_allowed_in_content_objects_subtree($right, $location)
    {
    	 return RightsUtilities :: is_allowed($right, $location, self :: TYPE_CONTENT_OBJECT, RepositoryManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_CONTENT_OBJECT);
    }
    
    static function create_content_objects_subtree_root_location()
    {
    	return RightsUtilities :: create_location('co_tree', RepositoryManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_CONTENT_OBJECT);
    }
    
	// External Repositories subtree
    
    static function create_location_in_external_repositories_subtree($name, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, RepositoryManager :: APPLICATION_NAME, self :: TYPE_CONTENT_OBJECT, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_EXTERNAL_REPOSITORY);
    }
    
    static function get_external_repositories_subtree_root()
    {
    	return RightsUtilities :: get_root(RepositoryManager :: APPLICATION_NAME, self :: TREE_TYPE_EXTERNAL_REPOSITORY, 0);
    }
    
	static function get_external_repositories_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(RepositoryManager :: APPLICATION_NAME, self :: TREE_TYPE_EXTERNAL_REPOSITORY, 0);
    }
    
    static function get_location_id_by_identifier_from_external_repositories_subtree($identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(RepositoryManager :: APPLICATION_NAME, self :: TYPE_CONTENT_OBJECT, $identifier, 0, self :: TREE_TYPE_EXTERNAL_REPOSITORY);
    }
    
	static function get_location_by_identifier_from_external_repositories_subtree($identifier)
    {
    	return RightsUtilities :: get_location_by_identifier(RepositoryManager :: APPLICATION_NAME, self :: TYPE_CONTENT_OBJECT, $identifier, 0, self :: TREE_TYPE_EXTERNAL_REPOSITORY);
    }
    
	static function is_allowed_in_external_repositories_subtree($right, $location)
    {
    	 return RightsUtilities :: is_allowed($right, $location, self :: TYPE_CONTENT_OBJECT, RepositoryManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_EXTERNAL_REPOSITORY);
    }
    
    static function create_external_repositories_subtree_root_location()
    {
    	return RightsUtilities :: create_location('ext_rep_tree', RepositoryManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_EXTERNAL_REPOSITORY);
    }
}
?>