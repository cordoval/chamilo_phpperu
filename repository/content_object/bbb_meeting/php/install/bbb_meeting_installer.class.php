<?php
namespace repository\content_object\bbb_meeting;

use repository\ContentObjectInstaller;

/**
 * $Id: bbb_meeting_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class BbbMeetingContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>