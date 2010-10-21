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
        
        //    	$question_context_paths = $page->get_question_context_paths();
        //    	dump('question context path');
        //    	dump($question_context_paths);
        

        // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();
        
        //        dump($page);
        

        $pageName = $page->getAttribute('id');
        //        dump($pageName);
        $paths = explode('page_', $pageName);
        $context_path = $paths[1];
        
        //        dump($context_path);
        

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
            //            dump('modal of data niet valid');
            return $page->handle('display');
        }
        // More pages?
        if (null !== ($nextName = $page->controller->getNextName($pageName)))
        {
            
            //            dump('next page: '.$nextName);
            

            $survey_values = $page->exportValues();
            //
//            dump('values:');
//            dump($survey_values);
            
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
            
//            dump($values);
            
            $complex_question_ids = array_keys($values);
            
//            dump($complex_question_ids);
            
            if (count($complex_question_ids) > 0)
            {
                foreach ($complex_question_ids as $complex_question_id)
                {
                    
//                    dump($complex_question_id);
                    $answers = $values[$complex_question_id];
                    
//                    dump($answers);
                    
                    if (count($answers) > 0)
                    {
                        $this->parent->save_answer($complex_question_id, serialize($answers), $context_path . '_' . $complex_question_id);
                    }
                }
            }
            
            $next = & $page->controller->getPage($nextName);
            
            //            dump($next);
            //            dump('handle jump');
            return $next->handle('jump');
            //             Consider this a 'finish' button, if there is no explicit one
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
            //            dump('display');
            return $page->handle('display');
        }
    
    }
}
?>