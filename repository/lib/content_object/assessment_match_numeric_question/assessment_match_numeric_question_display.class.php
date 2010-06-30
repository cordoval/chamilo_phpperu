<?php

/**
  * @package repository.lib.content_object.match_numeric_question
 */
require_once dirname(__FILE__) . '/main.php'; 

class AssessmentMatchNumericQuestionDisplay extends ContentObjectDisplay
{
	function get_description()
    {
        $object = $this->get_content_object();
        $options = $object->get_options();
    	
    	$html = array();
        
        $html[] = parent :: get_description();
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get('PossibleAnswer') . '</th>';
        $html[] = '<th>' . Translation :: get('Tolerance') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '<td>' . $option->get_tolerance() . '</td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }
}
?>