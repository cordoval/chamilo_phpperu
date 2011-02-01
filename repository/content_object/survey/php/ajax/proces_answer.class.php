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
    const PARAM_RESULTS = 'results';
    const PARAM_QUESTION_VISIBILITY = 'question_visibility';

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID, self :: PARAM_RESULTS);
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
            $question_selections = $this->process_question_results($question_results);
            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_page_id);
            
            $question_visibility = array();
            $complex_question_items = $survey_page->get_questions(true);
            
            while ($complex_question_item = $complex_question_items->next_result())
            {
                $id = $complex_question_item->get_id();
                
                if ($complex_question_item->is_visible())
                {
                    $question_visibility[$id] = true;
                }
                else
                {
                    $question_visibility[$id] = false;
                }
            }
            
            $configs = $survey_page->get_config();
            
            foreach ($question_selections as $question_id => $question_result)
            {
                $ids = explode('_', $question_id);
                $sqi = $ids[2];
                
                foreach ($configs as $config)
                {
                    $from_question_id = $config[SurveyPage :: FROM_VISIBLE_QUESTION_ID];
                    if ($sqi == $from_question_id)
                    {
                        $answer = $config[SurveyPage :: ANSWERMATCHES];
                        $answers_to_match = array();
                        foreach ($answer as $key => $value)
                        {
                            $answers_to_match[] = $key . '=' . $value;
                        }
                        
                        if (in_array($question_result, $answers_to_match))
                        {
                            foreach ($config[SurveyPage :: TO_VISIBLE_QUESTIONS_IDS] as $id)
                            {
                                $question_visibility[$id] = true;
                            }
                        }
                    }
                
                }
            
            }
            
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
        foreach ($question_results as $question_identifier => $question_value)
        {
            $question_identifier = explode('_', $question_identifier);
            $answer_ids = array_slice($question_identifier, 1);
            $answer_id = implode('_', $answer_ids);
            $question_type = $question_identifier[0];
            $question_id = $question_identifier[1];
            $question_name = 'survey_question_' . $question_id;
            $answer_match = $answer_id . '=' . $question_value;
            $contains_matches = (count(array_slice($question_identifier, 2, - 1)) > 1);
            $question_selections[$question_name] = $answer_match;
        }
        return $question_selections;
    }

}
?>