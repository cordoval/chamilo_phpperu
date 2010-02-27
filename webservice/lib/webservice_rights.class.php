<?php
/**
 * $Id: webservice_rights.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */

class WebserviceRights
{
    const USE_RIGHT = '1';

    static function get_available_rights()
    {
        $reflect = new ReflectionClass('WebserviceRights');
        return $reflect->getConstants();
    }

    static function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, 'admin');
    }

    static function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(WebserviceManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(WebserviceManager :: APPLICATION_NAME, $type, $identifier);
    }

    static function get_root_id()
    {
        return RightsUtilities :: get_root_id(WebserviceManager :: APPLICATION_NAME);
    }

    static function get_root()
    {
        return RightsUtilities :: get_root(WebserviceManager :: APPLICATION_NAME);
    }
    
    static function create_location_in_webservice_subtree($name, $type, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, WebserviceManager :: APPLICATION_NAME, $type, $identifier, 0, $parent, 0, 0, 'webservices_tree');
    }
}
?>