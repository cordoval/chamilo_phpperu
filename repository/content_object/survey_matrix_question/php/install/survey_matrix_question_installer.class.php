<?php
namespace repository\content_object\survey_matrix_question;

use repository\ContentObjectInstaller;

/**
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyMatrixQuestionContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>