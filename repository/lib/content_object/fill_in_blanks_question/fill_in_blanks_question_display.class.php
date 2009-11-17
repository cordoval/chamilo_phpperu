<?php
/**
 * $Id: fill_in_blanks_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.fill_in_blanks_question
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
        $answers = $object->get_answers();
        
        $html = array();
        
        $html[] = parent :: get_description();
        
        if ($object->get_question_type() == FillInBlanksQuestion :: TYPE_SELECT)
        {
            $answer_select = array();
            $answer_select[] = '<select name="answer">';
            foreach ($answers as $answer)
            {
                $value = substr($answer->get_value(), 1, - 1);
                $answer_select[] = '<option value="' . $value . '">' . $value . '</option>';
            }
            $answer_select[] = '</select>';
            
            foreach ($answers as $answer)
            {
                $answer_text = substr_replace($answer_text, implode("\n", $answer_select), strpos($answer_text, $answer->get_value(), $answer->get_position()), strlen($answer->get_value()));
            }
        }
        else
        {
            foreach ($answers as $answer)
            {
                $repeat = $answer->get_size() == 0 ? strlen($answer->get_value()) : $answer->get_size();
                $replacement = str_repeat('_', $repeat);
                $answer_text = substr_replace($answer_text, $replacement, strpos($answer_text, $answer->get_value(), $answer->get_position()), strlen($answer->get_value()));
            }
        }
        
        $html[] = $answer_text;
        
        return implode("\n", $html);
    }
}
?>