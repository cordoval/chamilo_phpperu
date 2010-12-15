<?php

namespace application\package;

use ReflectionClass;
use rights\RightsUtilities;
/**
 * $Id: package_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.package
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';

    const LOCATION_LANGUAGES = '1';
    const LOCATION_LANGUAGE_PACKS = '2';
    const LOCATION_TRANSLATOR_APPLICATIONS = '3';
    const LOCATION_VARIABLE_TRANSLATIONS = '4';
    const LOCATION_VARIABLES = '5';
    
    const TREE_TYPE_LANGUAGES = 1;
    const TYPE_LANGUAGE = 1;

    function get_available_rights()
    {
        $reflect = new ReflectionClass('PackageRights');

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

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, PackageManager :: APPLICATION_NAME);
    }

    function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(PackageManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(PackageManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_root_id()
    {
        return RightsUtilities :: get_root_id(PackageManager :: APPLICATION_NAME);
    }

    function get_root()
    {
        return RightsUtilities :: get_root(PackageManager :: APPLICATION_NAME);
    }

    function get_allowed_users($right, $identifier, $type)
    {
    	return RightsUtilities :: get_allowed_users($right, $identifier, $type, PackageManager :: APPLICATION_NAME);
    }

	static function create_location_in_languages_subtree($name, $type, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, PackageManager :: APPLICATION_NAME, $type, $identifier, 0, $parent, 0, 0, self :: TREE_TYPE_LANGUAGES);
    }

    static function get_languages_subtree_root()
    {
    	return RightsUtilities :: get_root(PackageManager :: APPLICATION_NAME, self :: TREE_TYPE_LANGUAGES);
    }

	static function get_languages_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(PackageManager :: APPLICATION_NAME, self :: TREE_TYPE_LANGUAGES);
    }

    static function get_location_id_by_identifier_from_languages_subtree($type, $identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(PackageManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_LANGUAGES);
    }

	static function is_allowed_in_languages_subtree($right, $location, $type)
    {
    	static $is_allowed = array(); // Caching already retrieved results, for speed.
    	if (!isset($is_allowed[$right][$location][$type]))
    	{
    		$is_allowed[$right][$location][$type] = RightsUtilities :: is_allowed($right, $location, $type, PackageManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_LANGUAGES);
    	}
    	return $is_allowed[$right][$location][$type];
    }
}
?>