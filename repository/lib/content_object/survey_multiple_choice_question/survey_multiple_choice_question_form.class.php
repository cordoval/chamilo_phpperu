<?php
/**
 * $Id: survey_multiple_choice_question_form.class.php $
 * @package repository.lib.content_object.survey_multiple_choice_question
 */
require_once PATH :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question_form.class.php';

class SurveyMultipleChoiceQuestionForm extends MultipleChoiceQuestionForm
{
	function create_content_object()
    {
        $object = new SurveyMultipleChoiceQuestion();
        return parent :: create_content_object($object);
    }
}
?>