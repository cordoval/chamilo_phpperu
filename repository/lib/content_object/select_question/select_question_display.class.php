<?php
/**
 * $Id: select_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.select_question
 */
class SelectQuestionDisplay extends ContentObjectDisplay
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
        
        $html[] = parent :: get_description();
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get($type == 'radio' ? 'SelectCorrectAnswer' : 'SelectCorrectAnswers') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $select_options = array();
        foreach ($options as $option)
        {
            $select_options[] = '<option>' . $option->get_value() . '</option>';
        }
        
        $html[] = '<tr>';
        $html[] = '<td>';
        $html[] = '<select style="width: 200px;"' . ($type == 'checkbox' ? ' multiple="true"' : '') . '>';
        $html[] = implode("\n", $select_options);
        $html[] = '</select>';
        $html[] = '</td>';
        $html[] = '</tr>';
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }
}
?>