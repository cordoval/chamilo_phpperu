<?php
/**
 * $Id: matching_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.matching_question
 */
class MatchingQuestionDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        return $html;
    }

    function get_description()
    {
        $content_object = $this->get_content_object();
        $matches = $content_object->get_matches();
        $options = $content_object->get_options();
        
        $html = array();
        $html[] = parent :: get_description();
        
        // Adding the matches
        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('PossibleMatches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode("\n", $table_header);
        
        $match_label = 'A';
        foreach ($matches as $index => $match)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $match_label . '</td>';
            $html[] = '<td>' . $match . '</td>';
            $html[] = '</tr>';
            $match_label ++;
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode("\n", $table_footer);
        
        $html[] = '<br />';
        
        // Adding the items to be matched
        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('MatchOptionAnswer') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode("\n", $table_header);
        
        $answer_count = 0;
        foreach ($options as $index => $option)
        {
            $answer_number = ($answer_count + 1) . '.';
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $answer_number . '</td>';
            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '</tr>';
            $answer_count ++;
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode("\n", $table_footer);
        
        return implode("\n", $html);
    }
}
?>