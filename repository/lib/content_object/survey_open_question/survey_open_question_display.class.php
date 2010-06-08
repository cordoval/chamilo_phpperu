<?php
/**
 * $Id: survey_open_question_display.class.php $
 * @package repository.lib.content_object.survey_open_question
 */
require_once PATH :: get_repository_path() . '/question_types/open_question/open_question_display.class.php';
/**
 * This class can be used to display open questions
 */
class SurveyOpenQuestionDisplay extends OpenQuestionDisplay
{
    function get_description()
    {
        $description = parent :: get_description();
        $object = $this->get_content_object();
        
        return '<b>' . $description;
    }
}
?>