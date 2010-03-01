<?php

/**
 * $Id: laika_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */


require_once Path :: get_application_path() . 'lib/laika/laika_manager/laika_manager.class.php';

class LaikaRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';
    
    const LOCATION_ANALYZER = 1;
    const LOCATION_BROWSER = 2;
    const LOCATION_GRAPHER = 3;
    const LOCATION_HOME = 4;
    const LOCATION_MAILER = 5;
    const LOCATION_TAKER = 6;
    const LOCATION_USER = 7;
    const LOCATION_VIEWER = 8;
    const LOCATION_INFORMER = 9;

    function get_available_rights()
    {
        $reflect = new ReflectionClass('LaikaRights');
        return $reflect->getConstants();
    }

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, LaikaManager :: APPLICATION_NAME);
    }
}
?>