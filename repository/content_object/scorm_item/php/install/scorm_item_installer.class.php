<?php
namespace repository\content_object\scorm_item;

use repository\ContentObjectInstaller;

/**
 * $Id: scorm_item_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class ScormItemContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>