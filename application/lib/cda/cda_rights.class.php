<?php
/**
 * $Id: cda_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.cda
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
require_once Path :: get_application_path() . 'lib/cda/cda_manager/cda_manager.class.php';

class CdaRights
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

    function get_available_rights()
    {
        $reflect = new ReflectionClass('CdaRights');
	    
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
        return RightsUtilities :: is_allowed($right, $location, $type, CdaManager :: APPLICATION_NAME);
    }
    
    function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(CdaManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(CdaManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_root_id()
    {
        return RightsUtilities :: get_root_id(CdaManager :: APPLICATION_NAME);
    }

    function get_root()
    {
        return RightsUtilities :: get_root(CdaManager :: APPLICATION_NAME);
    }
    
    function get_allowed_users($right, $identifier, $type)
    {
    	return RightsUtilities :: get_allowed_users($right, $identifier, $type, CdaManager :: APPLICATION_NAME);
    }
    
	static function create_location_in_languages_subtree($name, $type, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, CdaManager :: APPLICATION_NAME, $type, $identifier, 0, $parent, 0, 0, 'languages_tree');
    }
    
    static function get_languages_subtree_root()
    {
    	return RightsUtilities :: get_root(CdaManager :: APPLICATION_NAME, 'languages_tree');
    }
    
	static function get_languages_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(CdaManager :: APPLICATION_NAME, 'languages_tree');
    }
    
    static function get_location_id_by_identifier_from_languages_subtree($type, $identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(CdaManager :: APPLICATION_NAME, $type, $identifier, 0, 'languages_tree');
    }
    
	static function is_allowed_in_languages_subtree($right, $location, $type)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, CdaManager :: APPLICATION_NAME, null, 0, 'languages_tree');
    }
}
?>