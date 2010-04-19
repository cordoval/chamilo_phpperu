<?php
/**
 * $Id: survey_matrix_question.class.php
 * @package repository.lib.content_object.survey_matrix_question
 */
require_once PATH :: get_repository_path(). '/question_types/matrix_question/matrix_question.class.php';
require_once dirname(__FILE__) . '/survey_matrix_question_option.class.php';


class SurveyMatrixQuestion extends MatrixQuestion
{
const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
?>