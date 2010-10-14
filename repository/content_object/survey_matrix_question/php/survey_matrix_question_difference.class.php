<?php
namespace repository\content_object\survey_matrix_question;

use common\libraries\Path;
/**
 * $Id: survey_matrix_question_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_matrix_question
 */
require_once Path :: get_repository_path() . '/question_types/matrix_question/matrix_question_difference.class.php';
/**
 * This class can be used to get the difference between matrix questions
 */
class SurveyMatrixQuestionDifference extends MatrixQuestionDifference
{
}
?>