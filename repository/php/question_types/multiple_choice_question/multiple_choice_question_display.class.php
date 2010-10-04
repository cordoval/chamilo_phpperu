<?php
/**
 * $Id: multiple_choice_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.multiple_choice_question
 */
class MultipleChoiceQuestionDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        return $html;
    }

    function get_description()
    {
        $html = array();
        
        $lo = $this->get_content_object();
        $options = $lo->get_options();
        $type = $lo->get_answer_type();
        
        switch($type)
        {
        	case MultipleChoiceQuestion :: ANSWER_TYPE_RADIO : $type = 'radio'; break;
        	case MultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX : $type = 'checkbox'; break;
        }
        
        $html[] = parent :: get_description();
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('Option') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><input type="' . $type . '" name="option[]"/></td>';
            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }
}
?>