<?php
namespace repository\content_object\survey_open_question;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyOpenQuestionContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>