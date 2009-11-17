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