<?php
/**
 * $Id: fill_in_blanks_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
class FillInBlanksQuestionDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        return $html;
    }

    function get_description()
    {
        $object = $this->get_content_object();
        $answer_text = $object->get_answer_text();
        $clear_text = preg_split(FillInBlanksQuestionAnswer::CLOZE_REGEX, $answer_text);
        $answers = $object->get_answers();
        $questions = $object->get_number_of_questions();

        $question_answers = array();
        
        foreach($answers as $answer)
        {
        	$question_answers[$answer->get_position()][] = $answer;
        }
        
        $html = array();
        $html[] = parent :: get_description();
        
        for($i = 0; $i < $questions; $i++)
        {
	        $html[] = $clear_text[$i];
	        
        	if ($object->get_question_type() == FillInBlanksQuestion :: TYPE_SELECT)
	        {
	            $answer_select = array();
	            $answer_select[] = '<select name="answer">';
	            foreach ($question_answers[$i] as $answer)
	            {
	                $value = trim($answer->get_value());
	                $answer_select[] = '<option value="' . $value . '">' . $value . '</option>';
	            }
	            $answer_select[] = '</select>';
	            
	            $html[] = implode("\n", $answer_select);
	        }
	        else
	        {
	            foreach ($question_answers[$i] as $answer)
	            {
	                $repeat = $answer->get_size() == 0 ? strlen($answer->get_value()) : $answer->get_size();
	                $replacement = str_repeat('_', $repeat);
				    $html[] = $replacement;	            	
	            	
	            }
	        }
        }
        
        return implode("\n", $html);
    }
}
?>