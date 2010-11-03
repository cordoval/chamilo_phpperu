<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Path;
use repository\SelectQuestionDisplay;


/**
 * $Id: survey_select_question_display.class.php  $
 * @package repository.lib.content_object.survey_select_question
 */
require_once Path :: get_repository_path() . '/question_types/select_question/select_question_display.class.php';
require_once dirname ( __FILE__ ) . '/survey_select_question_option.class.php';

class SurveySelectQuestionDisplay extends SelectQuestionDisplay
{

}
?>