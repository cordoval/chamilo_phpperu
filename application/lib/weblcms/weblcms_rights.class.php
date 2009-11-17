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

    function get_available_rights()
    {
        $reflect = new ReflectionClass('WeblcmsRights');
        return $reflect->getConstants();
    }

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, WeblcmsManager :: APPLICATION_NAME);
    }

    function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(WeblcmsManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_root_id()
    {
        return RightsUtilities :: get_root_id(WeblcmsManager :: APPLICATION_NAME);
    }

    function get_root()
    {
        return RightsUtilities :: get_root(WeblcmsManager :: APPLICATION_NAME);
    }
}
?>