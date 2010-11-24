<?php
namespace repository\content_object\survey_matrix_question;

use common\libraries\Path;

use repository\MatrixQuestionOption;

/**
 * $Id: survey_matrix_question_option.class.php
 * @package repository.lib.content_object.survey_matrix_question
 */
require_once Path :: get_repository_path() . '/question_types/matrix_question/matrix_question_option.class.php';
/**
 * This class represents an option in a matrix question.
 */
class SurveyMatrixQuestionOption extends MatrixQuestionOption
{

}
?>