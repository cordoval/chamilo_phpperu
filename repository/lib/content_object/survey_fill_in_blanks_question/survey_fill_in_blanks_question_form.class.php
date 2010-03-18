<?php
/**
 * $Id: survey_fill_in_blanks_question_form.class.php  $
 * @package repository.lib.content_object.survey_fill_in_blanks_question
 */
require_once PATH::get_repository_path() . '/question_types/fill_in_blanks_question/fill_in_blanks_question_form.class.php';
require_once dirname(__FILE__) . '/survey_fill_in_blanks_question.class.php';
require_once dirname(__FILE__) . '/survey_fill_in_blanks_question_answer.class.php';

class SurveyFillInBlanksQuestionForm extends FillInBlanksQuestionForm
{
    function create_content_object()
    {
        $object = new SurveyFillInBlanksQuestion();
        return parent :: create_content_object($object);
    }
    
	function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        $matches = $this->get_matches($values['answer']);
        
        $i = 0;
        foreach ($matches as $position => $match)
        {
            $weight = $values['match_weight'][$i];
            $comment = $values['comment'][$i];
            $size = $values['size'][$i];
            $value = substr($match, 1, strlen($match) - 2);
            
            $options[] = new SurveyFillInBlanksQuestionAnswer($match, $weight, $comment, $size, $position);
            $i ++;
        }
        
        $object->set_answers($options);
    }
}
?>
