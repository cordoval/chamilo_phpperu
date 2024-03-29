<?php

namespace application\personal_calendar;

use common\libraries\WebApplication;
use common\libraries\Installer;

/**
 * $Id: personal_calendar_installer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.install
 */
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_data_manager.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PersonalCalendarInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, PersonalCalendarDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>
