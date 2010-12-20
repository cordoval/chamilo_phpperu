<?php
namespace repository\content_object\survey_description;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_description
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyDescriptionContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>