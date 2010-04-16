<?php
/**
 * $Id: assessment_matching_question.class.php $
 * @package repository.lib.content_object.matching_question
 */
require_once PATH :: get_repository_path() . '/question_types/matching_question/matching_question.class.php';

class AssessmentMatchingQuestion extends MatchingQuestion
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>