<?php
namespace repository\content_object\survey_multiple_choice_question;

use common\libraries\Path;
use repository\MultipleChoiceQuestionDisplay;


/**
 * $Id: survey_multiple_choice_question_display.class.php $
 * @package repository.lib.content_object.survey_multiple_choice_question
 */
require_once Path :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question_display.class.php';
require_once dirname ( __FILE__ ) . '/survey_multiple_choice_question_option.class.php';

class SurveyMultipleChoiceQuestionDisplay extends MultipleChoiceQuestionDisplay
{

}
?>