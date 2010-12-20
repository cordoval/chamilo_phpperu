<?php
namespace repository\content_object\survey_page;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyPageContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>