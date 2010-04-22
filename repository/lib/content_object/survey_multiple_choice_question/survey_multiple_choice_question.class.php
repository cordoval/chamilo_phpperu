<?php
/**
 * $Id: survey_multiple_choice_question.class.php $
 * @package repository.lib.content_object.survey_multiple_choice_question
 */
require_once PATH :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question.class.php';
require_once dirname(__FILE__) . '/survey_multiple_choice_question_option.class.php';

class SurveyMultipleChoiceQuestion extends MultipleChoiceQuestion
{
const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}   }
?>