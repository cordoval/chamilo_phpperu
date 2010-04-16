<?php
/**
 * $Id: assessment_matrix_question.class.php $
 * @package repository.lib.content_object.matrix_question
 */
require_once PATH :: get_repository_path(). '/question_types/matrix_question/matrix_question.class.php';


class AssessmentMatrixQuestion extends MatrixQuestion
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>