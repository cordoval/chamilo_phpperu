<?php

/**
 * $Id: laika_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */


require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/laika_manager.class.php';

class LaikaRights
{
    const RIGHT_VIEW = '1';
    const RIGHT_ADD = '2';
    const RIGHT_EDIT = '3';
    const RIGHT_DELETET = '4';

    const LOCATION_ANALYZER = 1;
    const LOCATION_BROWSER = 2;
    const LOCATION_GRAPHER = 3;
    const LOCATION_HOME = 4;
    const LOCATION_MAILER = 5;
    const LOCATION_TAKER = 6;
    const LOCATION_USER = 7;
    const LOCATION_VIEWER = 8;
    const LOCATION_INFORMER = 9;

    const TYPE_LAIKA_COMPONENT = 1;
    
    function get_available_rights()
    {
        $reflect = new ReflectionClass('LaikaRights');

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
        return RightsUtilities :: is_allowed($right, $location, $type, LaikaManager :: APPLICATION_NAME);
    }
}
?>