<?php
namespace repository\content_object\announcement;

use repository\ContentObjectInstaller;

/**
 * $Id: announcement_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class AnnouncementContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>