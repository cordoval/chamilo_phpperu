<?php
namespace repository\content_object\survey_matching_question;

use common\libraries\Path;
use repository\MatchingQuestionDisplay;


/**
 * $Id: survey_matching_question_display.class.php $
 * @package repository.lib.content_object.survey_matching_question
 */
require_once Path :: get_repository_path() . '/question_types/matching_question/matching_question_display.class.php';
require_once dirname(__FILE__) . '/survey_matching_question_option.class.php';

class SurveyMatchingQuestionDisplay extends MatchingQuestionDisplay
{

}
?>