<?php
namespace repository\content_object\assessment_rating_question;

use common\libraries\Path;
use repository\RatingQuestionDisplay;

/**
 * $Id: assessment_rating_question_display.class.php $
 * @package repository.lib.content_object.rating_question
 */
require_once Path :: get_repository_path() . '/question_types/rating_question/rating_question_display.class.php';

/**
 * This class can be used to display open questions
 */
class AssessmentRatingQuestionDisplay extends RatingQuestionDisplay
{
}
?>