<?php
/**
 * $Id: survey_open_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyOpenQuestionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();
        //$type = $question->get_question_type();
        $formvalidator = $this->get_formvalidator();

        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $table_header[] = '<tr>';
        $table_header[] = '<td>';
        $formvalidator->addElement('html', implode("\n", $table_header));

        $this->add_html_editor($clo_question, $formvalidator);

        $table_footer[] = '</td>';
        $table_footer[] = '</tr>';
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }

    function add_html_editor($clo_question, $formvalidator)
    {
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['toolbar'] = 'Assessment';

        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        $renderer = $this->get_renderer();

        $name = $clo_question->get_id() . '_0';
        $formvalidator->add_html_editor($name, '', false, $html_editor_options);
        $renderer->setElementTemplate($element_template, $name);
    }

    function get_instruction()
    {
        $question = $this->get_question();

        if ($question->has_description())
        {
            $instruction = Translation :: get('EnterAnswer');
        }
        else
        {
            $instruction = '';
        }

        return $instruction;
    }
}
?>