<?php
namespace repository\content_object\assessment_matrix_question;

use common\libraries\Path;

/**
 * $Id: assessment_matrix_question_display.class.php $
 * @package repository.lib.content_object.matrix_question
 */
require_once Path :: get_repository_path() . '/question_types/matrix_question/matrix_question_display.class.php';
require_once dirname (__FILE__) . '/assessment_matrix_question_option.class.php';

class AssessmentMatrixQuestionDisplay extends MatrixQuestionDisplay
{

}
?>