<?php
namespace repository\content_object\hotspot_question;

use repository\ContentObjectInstaller;

/**
 * $Id: hotspot_question_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class HotspotQuestionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>