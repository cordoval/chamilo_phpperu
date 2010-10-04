<?php

/**
 * $Id: laika_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

require_once Path :: get_help_path() . 'lib/help_manager/help_manager.class.php';

class HelpRights extends RightsUtilities
{
    
    const RIGHT_EDIT = '1';

    const TREE_TYPE_HELP = 1;
    const TYPE_HELP = 1;
    

    static function get_available_rights()
    {
//        $reflect = new ReflectionClass('HelpRights');
//
//        $rights = $reflect->getConstants();
//
//        foreach ($rights as $key => $right)
//        {
//            if (substr(strtolower($key), 0, 8) == 'location')
//            {
//                unset($rights[$key]);
//            }
//        }
//
//        return $rights;
        return parent :: get_available_rights(HelpManager :: APPLICATION_NAME);
    }

    static function get_available_types()
    {
        return parent :: get_available_types(HelpManager :: APPLICATION_NAME);
    }

    static function is_allowed($right, $location)
    {
        return RightsUtilities :: is_allowed($right, $location, self :: TYPE_HELP, HelpManager :: APPLICATION_NAME);
    }

    static function create_location_in_help_subtree($name, $identifier, $parent, $tree_identifier = 0)
    {
        return RightsUtilities :: create_location($name, HelpManager :: APPLICATION_NAME, self :: TYPE_HELP, $identifier, 1, $parent, 0, $tree_identifier, self :: TREE_TYPE_HELP);
    }

    static function get_help_subtree_root($tree_identifier = 0)
    {
        return RightsUtilities :: get_root(HelpManager :: APPLICATION_NAME, self :: TREE_TYPE_HELP, $tree_identifier);
    }

    static function get_help_subtree_root_id($tree_identifier = 0)
    {
        return RightsUtilities :: get_root_id(HelpManager :: APPLICATION_NAME, self :: TREE_TYPE_HELP, $tree_identifier);
    }

    static function get_location_id_by_identifier_from_help_subtree($identifier, $tree_identifier = 0)
    {
        return RightsUtilities :: get_location_id_by_identifier(HelpManager :: APPLICATION_NAME, self :: TYPE_HELP, $identifier, $tree_identifier, self :: TREE_TYPE_HELP);
    }

    static function is_allowed_in_help_subtree($right, $location, $tree_identifier = 0)
    {
        return RightsUtilities :: is_allowed($right, $location, self :: TYPE_HELP, HelpManager :: APPLICATION_NAME, null, $tree_identifier, self :: TREE_TYPE_HELP);
    }

    static function get_location_by_identifier_from_help_subtree($identifier, $tree_identifier = 0)
    {
        return RightsUtilities :: get_location_by_identifier(HelpManager :: APPLICATION_NAME, self :: TYPE_HELP, $identifier, $tree_identifier, self :: TREE_TYPE_HELP);
    }
    
}
?>