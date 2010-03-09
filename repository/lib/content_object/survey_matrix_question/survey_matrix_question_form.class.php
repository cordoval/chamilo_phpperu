<?php
/**
 * $Id: survey_matrix_question_form.class.php
 * @package repository.lib.content_object.survey_matrix_question
 */

require_once PATH :: get_repository_path() . '/question_types/matrix_question/matrix_question_form.class.php';

class SurveyMatrixQuestionForm extends MatrixQuestionForm
{
 	function create_content_object()
    {
        $object = new SurveyMatrixQuestion();
        return parent :: create_content_object($object);
    }
	
}
?>
