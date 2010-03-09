<?php
/**
 * $Id: survey_matching_question_form.class.php $
 * @package repository.lib.content_object.survey_matching_question
 */
require_once PATH :: get_repository_path() . '/question_types/matching_question/matching_question_form.class.php';

class SurveyMatchingQuestionForm extends MatchingQuestionForm
{
    /**
     * Adds the options and matches to the form
     */
    function create_content_object()
    {
        $object = new SurveyMatchingQuestion();
        return parent :: create_content_object($object);
    }
}
?>
