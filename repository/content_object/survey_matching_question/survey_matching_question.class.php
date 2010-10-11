<?php
/**
 * $Id: survey_matching_question.class.php $
 * @package repository.lib.content_object.survey_matching_question
 */
require_once PATH :: get_repository_path() . '/question_types/matching_question/matching_question.class.php';
require_once dirname(__FILE__) . '/survey_matching_question_option.class.php';


class SurveyMatchingQuestion extends MatchingQuestion
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}   
}
?>