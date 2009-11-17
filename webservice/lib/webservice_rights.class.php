<?php
/**
 * $Id: webservice_rights.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */

class WebserviceRights
{
    const USE_RIGHT = '1';

    function get_available_rights()
    {
        $reflect = new ReflectionClass('WebserviceRights');
        return $reflect->getConstants();
    }

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, 'admin');
    }

    function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier('webservice', $type, $identifier);
    }

    function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier('webservice', $type, $identifier);
    }

    function get_root_id()
    {
        return RightsUtilities :: get_root_id('webservice');
    }

    function get_root()
    {
        return RightsUtilities :: get_root('webservice');
    }
}
?>