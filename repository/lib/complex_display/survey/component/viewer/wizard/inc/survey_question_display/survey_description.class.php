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
        //$formvalidator->addElement('html', $detail);
    }

    function get_instruction()
    {
    
    }

    function add_header()
    {
    	
    	 $formvalidator = $this->formvalidator;
     
    $html[] = '<div class="assessment">';
        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
//        $html[] = '<div class="bevel">';
//        $html[] =  'test.';
//        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel">';
        $title = 'title';
        $html[] = $this->parse($title);
        
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
       $html[] = '<div class="answer">';
      
//        $description = $this->question->get_description();
//        if ($this->question->has_description())
//        {
//            $html[] = '<div class="description">';
//            
//            $html[] = $this->parse($description);
//            $html[] = '<div class="clear">&nbsp;</div>';
//            $html[] = '</div>';
//        }
        $html[] = '</div>'; 
       $html[] = '<div class="clear"></div>';
        
        $header = implode("\n", $html);
       $this->get_formvalidator()->addElement('html', $header);
   
    }
}
?>