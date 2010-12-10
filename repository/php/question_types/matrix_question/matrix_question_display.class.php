<?php
namespace repository;

use repository\ContentObject;
/**
 * $Id: matrix_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.matrix_question
 */
class MatrixQuestionDisplay extends ContentObjectDisplay
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
        $type = $content_object->get_matrix_type();

        $html = array();
        $html[] = parent :: get_description();

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption"></th>';

        foreach ($matches as $match)
        {
            $table_header[] = '<th class="center">' . strip_tags($match) . '</th>';
        }

        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode("\n", $table_header);

        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $option->get_value() . '</td>';

            foreach ($matches as $j => $match)
            {
                if ($type == MatrixQuestion :: MATRIX_TYPE_RADIO)
                {
                    $answer_name = $question_id . '_' . $index . '_0';
                    $html[] = '<td style="text-align: center;"><input type="radio" name="' . $answer_name . '"/></td>';
                }
                elseif ($type == MatrixQuestion :: MATRIX_TYPE_CHECKBOX)
                {
                    $answer_name = $question_id . '_' . $index . '[' . $j . ']';
                    $html[] = '<td style="text-align: center;"><input type="checkbox" name="' . $answer_name . '"/></td>';
                }
            }

            $html[] = '</tr>';
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode("\n", $table_footer);

        return implode("\n", $html);
    }
}
?>