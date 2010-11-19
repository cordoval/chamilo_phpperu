<?php
namespace repository\content_object\survey_multiple_choice_question;

use common\libraries\Path;

use repository\MultipleChoiceQuestionOption;

/**
 * $Id: survey_multiple_choice_question_option.class.php $
 * @package repository.lib.content_object.survey_multiple_choice_question
 */
require_once Path :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question_option.class.php';
/**
 * This class represents an option in a multiple choice question.
 */
class SurveyMultipleChoiceQuestionOption extends MultipleChoiceQuestionOption
{
}
?>