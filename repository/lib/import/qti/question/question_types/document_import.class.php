<?php
/**
 * $Id: document_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_import.class.php';

class DocumentQuestionQtiImport extends QuestionQtiImport
{

    function import_content_object()
    {
        $data = $this->get_file_content_array();
        
        $question = new OpenQuestion();
        $question->set_question_type(OpenQuestion :: TYPE_DOCUMENT);
        $title = $data['title'];
        //$descr = $data['itemBody']['uploadInteraction']['prompt'];
        $descr = parent :: get_tag_content('prompt');
        $question->set_title($title);
        $question->set_description(parent :: import_images($descr));
        //$question->create();
        parent :: create_question($question);
        return $question->get_id();
    }

}
?>