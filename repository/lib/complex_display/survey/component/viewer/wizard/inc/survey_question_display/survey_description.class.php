<?php

require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyDescriptionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $clo_question = $this->get_clo_question();
        $description = $this->get_question();
        $formvalidator = $this->get_formvalidator ();
        
        if ($description->has_description())
        {
            $html[] = '<div class="description">';
            $html[] = $this->parse($description->get_description());
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
}
?>