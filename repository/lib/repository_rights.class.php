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
    const VIEW_RIGHT = '4';
    const SEARCH_RIGHT = '5';
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

    function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(RepositoryManager :: APPLICATION_NAME, $type, $identifier);
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
        $repository_root = self :: get_root_id();
        
        $user_root = new Location();
        $user_root->set_location($user->get_username());
        $user_root->set_application(RepositoryManager :: APPLICATION_NAME);
        $user_root->set_type('user_root');
        $user_root->set_identifier($user->get_id());
        
        $user_root->set_parent($repository_root);
        if (! $user_root->create())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function get_user_root_id($user_id)
    {
        return self :: get_location_id_by_identifier('user_root', $user_id);
    }
}
?>