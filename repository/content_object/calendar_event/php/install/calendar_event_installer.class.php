<?php
namespace repository\content_object\calendar_event;

use repository\ContentObjectInstaller;

/**
 * $Id: calendar_event_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class CalendarEventContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>