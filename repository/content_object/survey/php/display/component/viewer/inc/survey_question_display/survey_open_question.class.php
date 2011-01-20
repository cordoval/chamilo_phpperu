<?php
namespace repository\content_object\survey;

use common\libraries\Translation;

require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyOpenQuestionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $complex_question = $this->get_complex_question();
        $question = $this->get_complex_question();
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

        $this->add_html_editor($question, $formvalidator);

        $table_footer[] = '</td>';
        $table_footer[] = '</tr>';
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }

    function add_html_editor($question, $formvalidator)
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

        $name = $question->get_id() . '_0';
        $formvalidator->add_html_editor($name, '', false, $html_editor_options);

        $answer = $this->get_answer();
        if ($answer)
        {
            $formvalidator->setDefaults(array($name => $answer[0]));
        }

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