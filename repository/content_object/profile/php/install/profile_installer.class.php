<?php
namespace repository\content_object\profile;

use repository\ContentObjectInstaller;

/**
 * $Id: profile_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class ProfileContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>