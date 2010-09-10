<?php
/**
 * $Id: survey_viewer_wizard_process.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard
 */
class SurveyViewerWizardNext extends HTML_QuickForm_Action
{
    private $parent;

    public function SurveyViewerWizardNext($parent)
    {
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
      
    	// save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();
        $pageName = $page->getAttribute('id');
        $data = & $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        if (PEAR :: isError($valid = $page->validate()))
        {
            return $valid;
        }
        $data['valid'][$pageName] = $valid;
        
        // Modal form and page is invalid: don't go further
        if ($page->controller->isModal() && ! $data['valid'][$pageName])
        {
            return $page->handle('display');
        }
        // More pages?
        if (null !== ($nextName = $page->controller->getNextName($pageName)))
        {
            
            $survey_values = $page->exportValues();
                     
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
            
            $keys = array_keys($values);
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
            $percent = $count_questions / $total_questions * 100;
//            $this->parent->get_parent()->finish_survey($percent);
            
            $next = & $page->controller->getPage($nextName);
            return $next->handle('jump');
            // Consider this a 'finish' button, if there is no explicit one
        }
        elseif ($page->controller->isModal())
        {
            if ($page->controller->isValid())
            {
                return $page->handle('process');
            }
            else
            {
                // this should redirect to the first invalid page
                return $page->handle('jump');
            }
        }
        else
        {
            
            return $page->handle('display');
        }
   
    }
}
?>