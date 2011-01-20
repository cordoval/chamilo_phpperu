<?php
namespace repository\content_object\survey_select_question;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveySelectQuestionContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>