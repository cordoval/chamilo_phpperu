<?php

namespace application\personal_calendar;
use common\libraries\Updater;
use common\libraries\WebApplication;
/**
 * $Id: personal_calendar_120_updater.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.install
 */
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_data_manager.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PersonalCalendar120Updater extends Updater
{

    /**
     * Constructor
     */
    function __construct($application)
    {
        parent :: __construct($application, PersonalCalendarDataManager :: get_instance());
    }
    
    function get_install_path()
    {
        return dirname(__FILE__) . '/../../install/';
    }
    
    function get_path()
    {
    	return dirname(__FILE__) . '/';
    }
}
?>