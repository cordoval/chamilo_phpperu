<?php
/**
 * $Id: survey_open_question_form.class.php $
 * @package repository.lib.content_object.survey_open_question
 */
require_once PATH :: get_repository_path() . '/question_types/open_question/open_question_form.class.php';

/**
 * This class represents a form to create or update open questions
 */
class SurveyOpenQuestionForm extends OpenQuestionForm
{
    // Inherited
    function create_content_object()
    {
        $object = new SurveyOpenQuestion();
        return parent :: create_content_object($object);
    }

}
?>