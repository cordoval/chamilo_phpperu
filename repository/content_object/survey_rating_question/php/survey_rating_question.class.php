<?php
namespace repository\content_object\survey_rating_question;

use common\libraries\Utilities;
use common\libraries\Path;

/**
 * $Id: survey_rating_question.class.php $
 * @package repository.lib.content_object.survey_rating_question
 */
require_once Path :: get_repository_path() . '/question_types/rating_question/rating_question.class.php';
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