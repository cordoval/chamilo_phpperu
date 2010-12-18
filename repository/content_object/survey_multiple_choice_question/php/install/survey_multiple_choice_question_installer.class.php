<?php
namespace repository\content_object\survey_multiple_choice_question;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyMultipleChoiceQuestionContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>