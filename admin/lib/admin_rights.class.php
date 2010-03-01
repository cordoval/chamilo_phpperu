<?php

/**
 * $Id: admin_rights.class.php 184 2009-11-13 09:51:32Z vanpouckesven $
 * @package admin.lib
 */
class AdminRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';
    
    const LOCATION_SYSTEM_ANNOUNECEMENTS = 1;
    const LOCATION_SETTINGS = 2;
    const LOCATION_CATEGORY_MANAGER = 3;

    function get_available_rights()
    {
        $reflect = new ReflectionClass('AdminRights');
        return $reflect->getConstants();
    }

    function is_allowed($right, $location = 0, $type = 'root')
    {
        return RightsUtilities :: is_allowed($right, $location, $type, 'admin');
    }
}
?>