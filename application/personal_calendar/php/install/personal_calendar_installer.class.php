<?php
/**
 * $Id: personal_calendar_installer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.install
 */
require_once dirname(__FILE__) . '/../personal_calendar_data_manager.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PersonalCalendarInstaller extends Installer
{

    /**
     * Constructor
     */
    function PersonalCalendarInstaller($values)
    {
        parent :: __construct($values, PersonalCalendarDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>