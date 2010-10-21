<?php
/**
 * $Id: survey_viewer_wizard_process.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard
 */
class SurveyViewerWizardProcess extends HTML_QuickForm_Action
{
    private $parent;

    public function SurveyViewerWizardProcess($parent)
    {
        //        dump($parent);
        //        exit;
        //    	$this->parent = $parent->get_parent();
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
        $this->parent->get_parent()->get_parent()->display_header();
        
        $html = array();
        $html[] = '<div class="assessment">';
        $html[] = '<h2>' . $this->parent->get_survey()->get_title() . '</h2>';
        $html[] = '</div>';

        $context_path = $page->get_context_path();
        
        $survey_values = $page->controller->exportValues();
       
        $values = array();
        
        foreach ($survey_values as $key => $value)
        {
            $value = Security :: remove_XSS($value);
            $split_key = split('_', $key);
            $count = count($split_key);
            $complex_question_id = $split_key[0];
            
            if (is_numeric($complex_question_id))
            {
                if (($value) || ($value == 0))
                {
                    $answer_index = $split_key[1];
                    if ($count == 3)
                    {
                        $sub_index = $split_key[2];
                        $values[$complex_question_id][$answer_index][$sub_index] = $value;
                    }
                    else
                    {
                        $values[$complex_question_id][$answer_index] = $value;
                    }
                
                }
            
            }
        }
        
        $complex_question_ids = array_keys($values);
        
        if (count($complex_question_ids) > 0)
        {
            foreach ($complex_question_ids as $complex_question_id)
            {
                $answers = $values[$complex_question_id];
                
                if (count($answers) > 0)
                {
                    $this->parent->save_answer($complex_question_id, serialize($answers), $context_path . '_' . $complex_question_id);
                }
            }
        }
        
        $this->parent->finished();
        
        //reset the controller !
        $page->controller->container(true);
        
        $html[] = '<div class="assessment">';
        $html[] = '<div class="description">';
        $finish_text = $this->parent->get_survey()->get_finish_text();
        $html[] = $this->parent->get_survey()->parse($context_path, $finish_text);
        
        $html[] = '</div></div>';
        
        $back_url = $this->parent->get_go_back_url();
        $html[] = '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
        
        echo implode("\n", $html);
        
        $this->parent->get_parent()->get_parent()->display_footer();
    }
}
?>