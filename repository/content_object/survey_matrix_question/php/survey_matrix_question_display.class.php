<?php
namespace repository\content_object\survey_matrix_question;

use common\libraries\Path;
use repository\ContentObjectDisplay;

/**
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
require_once dirname(__FILE__) . '/survey_matrix_question_option.class.php';

class SurveyMatrixQuestionDisplay extends ContentObjectDisplay
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
                if ($type == SurveyMatrixQuestion :: MATRIX_TYPE_RADIO)
                {
                    $answer_name = $question_id . '_' . $index . '_0';
                    $html[] = '<td style="text-align: center;"><input type="radio" name="' . $answer_name . '"/></td>';
                }
                elseif ($type == SurveyMatrixQuestion :: MATRIX_TYPE_CHECKBOX)
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