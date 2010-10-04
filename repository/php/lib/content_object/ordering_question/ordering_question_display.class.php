<?php
/**
 * $Id: ordering_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.ordering_question
 */
class OrderingQuestionDisplay extends ContentObjectDisplay
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
        
        $html[] = parent :: get_description();
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('PutAnswersCorrectOrder') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $order_options = $this->get_order_options();
        
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>';
            $html[] = '<select>';
            $html[] = $order_options;
            $html[] = '</select>';
            $html[] = '</td>';
            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }

    function get_order_options()
    {
        $answer_count = count($this->get_content_object()->get_options());
        
        $options = array();
        for($i = 1; $i <= $answer_count; $i ++)
        {
            $options[] = '<option>' . $i . '</option>';
        }
        
        return implode("\n", $options);
    }
}
?>