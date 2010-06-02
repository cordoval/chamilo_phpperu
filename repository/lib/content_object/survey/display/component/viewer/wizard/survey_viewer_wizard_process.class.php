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
        
//        if ($this->parent->get_survey()->has_description())
//        {
//            
//            $description = $this->parent->get_survey()->get_description();
//            $html[] = '<div class="description">';
//            $html[] = $this->parent->get_parent()->parse($description);
//            $html[] = '<div class="clear"></div>';
//            $html[] = '</div>';
//        }
        $html[] = '</div>';
        
        $survey_values = $page->controller->exportValues();
        
//        dump($survey_values);
        
        $values = array();
        
        foreach ($survey_values as $key => $value)
        {
            $value = Security :: remove_XSS($value);
            $split_key = split('_', $key);
            $count = count($split_key);
            $question_id = $split_key[0];
            
            if (is_numeric($question_id))
            {
                if (($value) || ($value == 0))
                {
                    $answer_index = $split_key[1];
                    
                    if ($count == 4)
                    {
                        $sub_index = $split_key[2];
                        $values[$question_id][$answer_index][$sub_index] = $value;
                    }
                    else
                    {
                        $values[$question_id][$answer_index] = $value;
                    }
                
                }
            
            }
        }
        
        //$question_numbers = $_SESSION['questions'];
//        dump($values);

        $keys = array_keys($values);
        
//        dump($keys);
        
        $count_questions = 0;
        
         if (count($keys) > 0)
        {
            $rdm = RepositoryDataManager :: get_instance();
            
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $keys, ContentObject :: get_table_name());
            $questions_ccoi = $rdm->retrieve_content_objects($condition);
            
            while ($question_ccoi = $questions_ccoi->next_result())
            {
                
                if (get_class($question_ccoi) != 'ComplexSurvey')
                {
                    $answers = $values[$question_ccoi->get_id()];
                    
                    if (count($answers) > 0)
                    {
                        //$question = $rdm->retrieve_content_object($question_ccoi->get_ref());
                        $count_questions ++;
                        $this->parent->get_parent()->save_answer($question_ccoi->get_id(), serialize($answers));
                    }
                
                }
            
            }
        }
               
        
        
        $total_questions = $this->parent->get_total_questions();
        
//        dump($total_questions);
//        
//        dump($count_questions);
        
//        $percent = $count_questions / $total_questions * 100;
        
	//before we make questions required, sending the answers makes the survey finished;
        $percent = 100;
        $this->parent->get_parent()->finish_survey($percent);
        
//        dump($percent);
//        exit;
        
        //reset the controller !
        $page->controller->container(true);
        
        $html[] = '<div class="assessment">';
        $html[] = '<div class="description">';
        $finish_text = $this->parent->get_survey()->get_finish_text();
        $html[] = $this->parent->get_parent()->parse($finish_text);
        
        $html[] = '</div></div>';
        
//        $back_url = $this->parent->get_parent()->get_go_back_url();
//        
//        $html[] = '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
        
        echo implode("\n", $html);
        
        $this->parent->get_parent()->get_parent()->display_footer();
    }
}
?>