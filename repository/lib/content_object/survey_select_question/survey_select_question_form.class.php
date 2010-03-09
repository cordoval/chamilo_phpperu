<?php
/**
 * $Id: survey_select_question_form.class.php $
 * @package repository.lib.content_object.survey_select_question
 */
require_once PATH :: get_repository_path() . '/question_types/select_question/select_question_form.class.php';

class SurveySelectQuestionForm extends SelectQuestionForm
{
    function create_content_object()
    {
        $object = new SurveySelectQuestion();
		return parent :: create_content_object($object);
    }  
}
?>