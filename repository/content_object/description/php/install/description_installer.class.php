<?php
namespace repository\content_object\description;

use repository\ContentObjectInstaller;

/**
 * $Id: description_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class DescriptionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>