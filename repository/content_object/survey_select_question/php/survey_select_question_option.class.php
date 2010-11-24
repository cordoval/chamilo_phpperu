<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Path;

use repository\SelectQuestionOption;

/**
 * $Id: survey_select_question_option.class.php $
 * @package repository.lib.content_object.survey_select_question
 */
require_once Path :: get_repository_path() . '/question_types/select_question/select_question_option.class.php';
/**
 * This class represents an option in a multiple choice question.
 */
class SurveySelectQuestionOption extends SelectQuestionOption
{

}
?>