<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/profiler_manager/profiler_manager.class.php';

/**
 * Provides the rights and locations of the profiler application
 *
 * @author Pieterjan Broekaert
 */
class ProfilerRights
{
    /*
     * The available rights
     */
    const RIGHT_PUBLISH = 1;
    const RIGHT_EDIT = 2;
    const RIGHT_DELETE = 3;
    const RIGHT_EDIT_RIGHTS = 4;

    /*
     * The tree that contains the dynamic locations
     */
    const TREE_TYPE_PROFILER = 1;

    /*
     * The type of locations available
     */
    const TYPE_CATEGORY = 1;
    const TYPE_PUBLICATION = 2;

    static function get_available_rights()
    {
        return array('Publish' => self :: RIGHT_PUBLISH, 'Edit' => self :: RIGHT_EDIT, 'Delete' => self::RIGHT_DELETE, 'Edit Rights' => self::RIGHT_EDIT_RIGHTS);
    }

    static function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, ProfilerManager :: APPLICATION_NAME);
    }

    static function create_location_in_profiler_subtree($name, $identifier, $parent, $type)
    {
        return RightsUtilities :: create_location($name, ProfilerManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_PROFILER);
    }

    static function get_profiler_subtree_root()
    {
        return RightsUtilities :: get_root(ProfilerManager :: APPLICATION_NAME, self :: TREE_TYPE_PROFILER, 0);
    }

    static function get_profiler_subtree_root_id()
    {
        return RightsUtilities :: get_root_id(ProfilerManager :: APPLICATION_NAME, self :: TREE_TYPE_PROFILER, 0);
    }

    static function get_location_id_by_identifier_from_profiler_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_id_by_identifier(ProfilerManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_PROFILER);
    }

    static function get_location_by_identifier_from_profiler_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_by_identifier(ProfilerManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_PROFILER);
    }

    static function is_allowed_in_profiler_subtree($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, ProfilerManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_PROFILER);
    }

    static function create_profiler_subtree_root_location()
    {
        return RightsUtilities :: create_location('profiler_tree', ProfilerManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_PROFILER);
    }
}

?>
