<?php
/**
 * $Id: weblcms_rights.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class WeblcmsRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';

    static function get_available_rights()
    {
        $reflect = new ReflectionClass('WeblcmsRights');
        return $reflect->getConstants();
    }

    static function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, WeblcmsManager :: APPLICATION_NAME);
    }

    static function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_root_id()
    {
        return RightsUtilities :: get_root_id(WeblcmsManager :: APPLICATION_NAME);
    }

    static function get_root()
    {
        return RightsUtilities :: get_root(WeblcmsManager :: APPLICATION_NAME);
    }
    
	static function create_location_in_courses_subtree($name, $type, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, WeblcmsManager :: APPLICATION_NAME, $type, $identifier, 0, $parent, 0, 0, 'courses_tree');
    }
    
    static function get_courses_subtree_root()
    {
    	return RightsUtilities :: get_root(WeblcmsManager :: APPLICATION_NAME, 'courses_tree');
    }
    
	static function get_courses_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(WeblcmsManager :: APPLICATION_NAME, 'courses_tree');
    }
    
    static function get_location_id_by_identifier_from_courses_subtree($type, $identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier, 0, 'courses_tree');
    }
    
	static function is_allowed_in_courses_subtree($right, $location, $type)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, WeblcmsManager :: APPLICATION_NAME, null, 0, 'courses_tree');
    }
}
?>