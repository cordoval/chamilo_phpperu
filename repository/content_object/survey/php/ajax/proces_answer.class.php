<?php
namespace repository\content_object\survey;

use common\libraries\Utilities;
use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;
use common\libraries\Path;
use repository\content_object\survey_page\SurveyPage;

use repository\RepositoryDataManager;

/**
 * @package repository.content_object.assessment;
 */

class SurveyAjaxProcesAnswer extends AjaxManager
{
    
    const PARAM_SURVEY_PAGE_ID = 'survey_page';
    const PARAM_SURVEY_PUBLICATION_ID = 'survey_publication';
    const PARAM_RESULTS = 'results';
    const PARAM_QUESTION_VISIBILITY = 'question_visibility';
    
    const PROPERTY_HINT = 'hint';
    const PROPERTY_ELEMENT_NAME = 'element_name';

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID, self :: PARAM_SURVEY_PUBLICATION_ID, self :: PARAM_RESULTS);
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        
        $survey_page_id = $this->get_parameter(self :: PARAM_SURVEY_PAGE_ID);
        $question_results = $this->get_parameter(self :: PARAM_RESULTS);
        $question_results = json_decode($question_results, true);
        
        if (count($question_results) > 0)
        {
            
            $result;
            foreach ($question_results as $key => $value)
            {
                $result = $key;
            }
            //	dump($result);
            $question_identifier = explode('_', $result);
            
            $page_index = end($question_identifier);
            
            $question_selections = $this->process_question_results($question_results);
            
            /**
             * Verification of question visiblity goes here.
             *
             * Expected format for $question_visibility:
             * A single dimension array containing the question ids
             * (survey_question_x) as keys and a boolean as a value
             *
             * The example below just loops through all questions
             * with selected answers and hides them
             */
            
            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_page_id);
            //dump($page_index);
            //  dump($survey_page);
            //    dump($question_results);
            //exit;
            

            $question_visibility = array();
            $complex_question_items = $survey_page->get_questions(true);
            
            while ($complex_question_item = $complex_question_items->next_result())
            {
                $question_id = $complex_question_item->get_ref();
                //		$id = 'survey_question_' . $question_id;
//                $id = 'survey_question_' . $complex_question_item->get_id();
                
                $id = $complex_question_item->get_id();
                
                if ($complex_question_item->get_visible() == 1)
                {
                    $question_visibility[$id] = true;
                }
                else
                {
                    $question_visibility[$id] = false;
                }
            }
            //	dump($question_visibility);
            $configs = $survey_page->get_config();
                      
            //
            //    $question_visibility = array();
            $rconfig;
            $resultq;
            $ranswer;
            
            //	dump($configs);
            //	dump($question_selections);
            //	exit;
            foreach ($question_selections as $question_id => $question_result)
            {
                
                $resultq = $ids = explode('_', $question_id);
                $sqi = $ids[2];
                
                foreach ($configs as $config)
                {
                    $rconfig = $config;
                    $from_question_id = $config[SurveyPage :: FROM_VISIBLE_QUESTION_ID];
                    if ($sqi == $from_question_id)
                    {
                        $answer = $config[SurveyPage :: ANSWERMATCHES];
                        //				dump($answer);
                        $answers_to_match = array();
                        foreach ($answer as $key => $value)
                        {
                            $oids = explode('_', $key);
                            //					dump ( $oids );
                            if (count($oids) == 3)
                            {
                                $answers_to_match[] = $oids[1];
                            }
                            elseif (count($oids) == 4)
                            {
                                //						dump($oids);
                                $option = $oids[1];
                                $answers_to_match[$option] = $value;
                            
     //						$answers_to_match [$option] = $oids [2];
                            

                            }
                        }
                        //				dump ( $answers_to_match );
                        //
                        //				dump ( $question_result );
                        

                        if (! empty($question_result))
                        {
                            if (! is_array($question_result))
                            {
                                $question_result = array($question_result);
                            }
                        }
                        
                        //				dump ( $question_result );
                        

                        //				foreach ( $question_result as $key => $value ) {
                        //					dump ( $key );
                        //					dump($value);
                        //				}
                        

                        $diff = array_diff($question_result, $answers_to_match);
                        if (count($diff) == 0)
                        {
                            foreach ($config[SurveyPage :: TO_VISIBLE_QUESTIONS_IDS] as $id)
                            {
//                                $qid = 'survey_question_' . $id;
                                 $qid = $id;
                                $question_visibility[$qid] = true;
                            }
                        }
                    }
                
                }
            
     //	    	$question_visibility['survey_question_62'] = true;
            }
            //
            //	    dump($question_visibility);
            //   dump($resultq);
            //    dump($rconfig);
            //	exit;
            

            //
            $result = new JsonAjaxResult(200);
        	$result->set_property(self :: PARAM_QUESTION_VISIBILITY, $question_visibility);
        	$result->display();
           
        }
        else
        {
            $result = new JsonAjaxResult(200);
        	$result->set_property(self :: PARAM_QUESTION_VISIBILITY, array());
        	$result->display();
        }
    
    }

    function process_question_results($question_results)
    {
        $question_selections = array();
//       	dump($question_results);
        foreach ($question_results as $question_identifier => $question_value)
        {
            $question_identifier = explode('_', $question_identifier);
            $question_type = $question_identifier[0];
            $question_id = $question_identifier[1];
            $question_name = 'survey_question_' . $question_id;
            
            $contains_matches = (count(array_slice($question_identifier, 2, - 1)) > 1);
            
            if ($question_type == 'radio')
            {
                if ($contains_matches)
                {
                    $question_selections[$question_name][$question_identifier[2]] = $question_value;
                }
                else
                {
                    $question_selections[$question_name] = $question_value;
                }
            }
            elseif ($question_type == 'checkbox')
            {
                if ($contains_matches)
                {
                    $question_selections[$question_name][$question_identifier[2]][] = $question_identifier[3];
                }
                else
                {
                    $question_selections[$question_name][] = $question_identifier[2];
                }
            }
        }
        
        return $question_selections;
    }

}
?>