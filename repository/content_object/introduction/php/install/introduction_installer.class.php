<?php
namespace repository\content_object\introduction;

use repository\ContentObjectInstaller;

/**
 * $Id: introduction_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class IntroductionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>