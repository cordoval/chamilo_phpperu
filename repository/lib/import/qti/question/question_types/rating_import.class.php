<?php
/**
 * $Id: rating_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_import.class.php';

class RatingQuestionQtiImport extends QuestionQtiImport
{

    function import_content_object()
    {
        $data = $this->get_file_content_array();
        
        $title = $data['title'];
        //$descr = $data['itemBody']['sliderInteraction']['prompt'];
        $description = parent :: get_tag_content('prompt');
        $low = $data['itemBody']['sliderInteraction']['lowerBound'];
        $high = $data['itemBody']['sliderInteraction']['upperBound'];
        $question = new RatingQuestion();
        $question->set_title($title);
        $question->set_description(parent :: import_images($description));
        $question->set_high($high);
        $question->set_low($low);
        parent :: create_question($question);
        return $question->get_id();
    }
}
?>