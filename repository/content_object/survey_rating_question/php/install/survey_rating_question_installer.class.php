<?php
namespace repository\content_object\survey_rating_question;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_rating_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyRatingQuestionContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>