<?php
require_once dirname(__FILE__) . '/../peer_assessment_question_display.class.php';

class PeerAssessmentMatrixQuestionDisplay extends PeerAssessmentQuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();

        $options = $question->get_options();
        $matches = $question->get_matches();
        $type = $question->get_matrix_type();

        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption" style="width: 30%;"></th>';

        foreach ($matches as $match)
        {
            $table_header[] = '<th class="center">' . strip_tags($match) . '</th>';
        }

        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));

        $question_id = $clo_question->get_id();

        foreach ($options as $i => $option)
        {
            $group = array();

            $group[] = $formvalidator->createElement('static', null, null, '<div style="text-align: left;">' . $option->get_value() . '</div>');

            foreach ($matches as $j => $match)
            {
                if ($type == MatrixQuestion :: MATRIX_TYPE_RADIO)
                {
                    $answer_name = $question_id . '_' . $i . '_0_'.$this->get_page_nr();
                    $group[] = $formvalidator->createElement('radio', $answer_name, null, null, $j);
                }
                elseif ($type == MatrixQuestion :: MATRIX_TYPE_CHECKBOX)
                {
                    $answer_name = $question_id . '_' . $i . '_' . $j . '_'.$this->get_page_nr();
                    $group[] = $formvalidator->createElement('checkbox', $answer_name);
                }
            }

            $formvalidator->addGroup($group, 'matrix_option_' . $i, null, '', false);

            $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'matrix_option_' . $i);
            $renderer->setGroupElementTemplate('<td style="text-align: center;">{element}</td>', 'matrix_option_' . $i);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }

    function add_border()
    {
        return false;
    }

    function get_instruction()
    {

    }
}
?>