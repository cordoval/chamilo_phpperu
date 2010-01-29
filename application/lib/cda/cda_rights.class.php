<?php

/**
 * $Id: cda_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */


require_once Path :: get_application_path() . 'lib/cda/cda_manager/cda_manager.class.php';

class CdaRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';

    function get_available_rights()
    {
        $reflect = new ReflectionClass('CdaRights');
        return $reflect->getConstants();
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
}
?>