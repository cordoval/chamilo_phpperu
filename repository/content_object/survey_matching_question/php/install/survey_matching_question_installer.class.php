<?php
namespace repository\content_object\survey_matching_question;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_matching_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyMatchingQuestionContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>