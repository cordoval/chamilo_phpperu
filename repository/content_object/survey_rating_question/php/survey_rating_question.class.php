<?php
/**
 * $Id: survey_rating_question.class.php $
 * @package repository.lib.content_object.survey_rating_question
 */
require_once PATH :: get_repository_path() . '/question_types/rating_question/rating_question.class.php';
/**
 * This class represents an open question
 */
class SurveyRatingQuestion extends RatingQuestion
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}    
}
?>