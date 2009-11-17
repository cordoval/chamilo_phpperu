<?php
/**
 * $Id: ordering_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_import.class.php';

class OrderingQuestionQtiImport extends QuestionQtiImport
{

    function import_content_object()
    {
        $data = $this->get_file_content_array();
        $title = $data['title'];
        
        $description = parent :: get_tag_content('prompt');
        $description = parent :: import_images($description);
        $question = new OrderingQuestion();
        $question->set_title($title);
        $question->set_description($description);
        
        $this->create_answers($data, $question);
        parent :: create_question($question);
        return $question->get_id();
    }

    function create_answers($data, $question)
    {
        $orders = $data['responseDeclaration']['correctResponse']['value'];
        $answers = $data['itemBody']['orderInteraction']['simpleChoice'];
        
        foreach ($answers as $answer)
        {
            $value = $answer['_content'];
            $id = $answer['identifier'];
            foreach ($orders as $i => $identifier)
            {
                if ($identifier == $id)
                {
                    $order = ($i + 1);
                    break;
                }
            }
            
            $ordering_option = new OrderingQuestionOption($value, $order);
            $question->add_option($ordering_option);
        }
    }
}
?>