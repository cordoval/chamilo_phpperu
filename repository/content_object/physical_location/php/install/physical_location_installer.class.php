<?php
namespace repository\content_object\physical_location;

use repository\ContentObjectInstaller;

/**
 * $Id: physical_location_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class PhysicalLocationContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>