<?php
namespace repository\content_object\survey_page;

use repository\ContentObjectInstaller;

/**
 * $Id: survey_page_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class SurveyPageContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>