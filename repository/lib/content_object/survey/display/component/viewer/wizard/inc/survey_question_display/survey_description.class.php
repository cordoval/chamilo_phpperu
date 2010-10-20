<?php

require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyDescriptionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $complex_question = $this->get_complex_question();
        $question = $this->get_question();
        $formvalidator = $this->get_formvalidator ();

        if ($question->has_description())
        {
            $html[] = '<div class="assessment">';
            $html[] = '<div class="information">';
            $html[] = $this->parse($question->get_description());

            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';

        }

        $detail = implode("\n", $html);
        $formvalidator->addElement('html', $detail);
    }

    function get_instruction()
    {

    }

    function add_header()
    {
    }

    function add_footer()
    {
    }
}
?>