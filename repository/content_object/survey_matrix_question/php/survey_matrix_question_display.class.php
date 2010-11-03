<?php
namespace repository\content_object\survey_matrix_question;

use common\libraries\Path;
use repository\MatrixQuestionDisplay;


/**
 * $Id: survey_matching_question_display.class.php $
 * @package repository.lib.content_object.survey_matrix_question
 */
require_once Path :: get_repository_path() . '/question_types/matrix_question/matrix_question_display.class.php';
require_once dirname ( __FILE__ ) . '/survey_matrix_question_option.class.php';

class SurveyMatrixQuestionDisplay extends MatrixQuestionDisplay
{

}