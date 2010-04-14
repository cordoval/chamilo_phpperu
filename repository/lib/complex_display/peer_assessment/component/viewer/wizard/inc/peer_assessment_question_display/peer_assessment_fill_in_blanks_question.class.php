<?php
require_once dirname(__FILE__) . '/../peer_assessment_question_display.class.php';

class PeerAssessmentFillInBlanksQuestionDisplay extends PeerAssessmentQuestionDisplay
{

    function add_question_form()
    {
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $answsers = $question->get_answers();
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        
        $answer_text = $question->get_answer_text();
        $answer_text = nl2br($answer_text);
        
        $question_type = $question->get_question_type();
        $answer_options = $this->get_possible_answers();
        
        $matches = array();
        preg_match_all('/\[[a-zA-Z0-9_êëûüôöîïéèà\s\-]*\]/', $answer_text, $matches);
        $matches = $matches[0];
        foreach ($matches as $i => $match)
        {
            $name = $clo_question->get_id() . '_' . $i.'_'.$this->get_page_nr();
            
            if ($question_type == FillInBlanksQuestion :: TYPE_SELECT)
            {
                //$element_options = $this->shuffle_with_keys($answer_options);
                $element = $formvalidator->createElement('select', $name, null, $answer_options);
            }
            else
            {
                $answer = $answsers[$i];
                $size = $answer->get_size();
                
                if ($size == 0)
                    $size = strlen($match) - 2;
                
                $element = $formvalidator->createElement('text', $name, '', array('size' => $size));
            
            }
            
            $pos = strpos($answer_text, $match);
            $formvalidator->addElement('html', substr($answer_text, 0, $pos));
            $formvalidator->addElement($element);
            $start = $pos + strlen($match);
            $answer_text = substr($answer_text, $start, strlen($answer_text) - $start);
            $renderer->setElementTemplate('{element}', $name);
            
        //$answer_text = str_replace($match, $element->toHtml(), $answer_text);
        }
        
        $formvalidator->addElement('html', $answer_text);
        
        //$formvalidator->addElement('static', 'blanks', null, $answer_text);
        //$formvalidator->addElement('html', $answer_text);
        $renderer->setElementTemplate($element_template, 'blanks');
    }

    function add_borders()
    {
        return true;
    }

    function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();
        
        if ($question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('FillInTheBlanks');
            $instruction[] = '</div>';
        }
        else
        {
            $instruction = array();
        }
        
        return implode("\n", $instruction);
    }

    function get_possible_answers()
    {
        $answers = $this->get_question()->get_answers();
        $options = array();
      
        foreach ($answers as $answer)
        {
            $option = str_replace(array('[', ']'), '', $answer->get_value());
            $options[$option] = $option;
        }
        
        asort($options);
        
        return $options;
    }
}
?>