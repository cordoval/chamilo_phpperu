<?php
namespace repository\content_object\survey_matching_question;

use common\libraries\Path;

use repository\MatchingQuestionOption;

/**
 * $Id: survey_matching_question_option.class.php $
 * @package repository.lib.content_object.survey_matching_question
 */
require_once Path :: get_repository_path() . '/question_types/matching_question/matching_question_option.class.php';
/**
 * This class represents an option in a matching question.
 */
class SurveyMatchingQuestionOption extends MatchingQuestionOption
{

}
?>