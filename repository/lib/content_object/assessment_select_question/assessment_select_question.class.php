<?php
/**
 * $Id: assessment_select_question.class.php $
 * @package repository.lib.content_object.select_question
 */
require_once PATH :: get_repository_path() . '/question_types/select_question/select_question.class.php';

class AssessmentSelectQuestion extends SelectQuestion
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>