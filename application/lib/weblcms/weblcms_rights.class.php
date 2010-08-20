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

    const LOCATION_BROWSER = 1;
	const LOCATION_HOME = 2;
	const LOCATION_VIEWER = 3;

	const TYPE_COURSE = 1;

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

	static function create_location_in_courses_subtree($name, $type, $identifier, $parent, $tree_identifier = 0)
    {
    	return RightsUtilities :: create_location($name, WeblcmsManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, $tree_identifier, WeblcmsRights :: TYPE_COURSE);
    }

    static function get_courses_subtree_root($tree_identifier = 0)
    {
    	return RightsUtilities :: get_root(WeblcmsManager :: APPLICATION_NAME, WeblcmsRights :: TYPE_COURSE, $tree_identifier);
    }

	static function get_courses_subtree_root_id($tree_identifier = 0)
    {
    	return RightsUtilities :: get_root_id(WeblcmsManager :: APPLICATION_NAME, WeblcmsRights :: TYPE_COURSE, $tree_identifier);
    }

    static function get_location_id_by_identifier_from_courses_subtree($type, $identifier, $tree_identifier = 0)
    {
    	return RightsUtilities :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier, $tree_identifier, WeblcmsRights :: TYPE_COURSE);
    }

	static function is_allowed_in_courses_subtree($right, $location, $type, $tree_identifier = 0)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, WeblcmsManager :: APPLICATION_NAME, null, $tree_identifier, WeblcmsRights :: TYPE_COURSE);
    }
}
?>