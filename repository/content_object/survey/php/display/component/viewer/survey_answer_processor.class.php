<?php
namespace repository\content_object\survey;

use repository\ComplexContentObjectItem;
use common\libraries\InCondition;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use repository\RepositoryDataManager;
use common\libraries\FormValidator;
use common\libraries\Security;
use common\libraries\StringUtilities;

class SurveyAnswerProcessor
{
    /**
     * @var SurveyDisplaySurveyViewerComponent
     */
    private $survey_viewer;

    /**
     * @var array
     */
    private $question_results = array();

    function __construct(SurveyDisplaySurveyViewerComponent $survey_viewer)
    {
        $this->survey_viewer = $survey_viewer;
    }

    function get_page_number()
    {
        return $this->get_survey_viewer()->get_questions_page();
    }

    function save_answers()
    {
       
//    	$this->survey_viewer->save_answer($question_id, $answer, $context_path);

      

//        $questions_cloi = $this->survey_viewer->get_questions($results_page_number);
       
        
        $post_values = $_POST;
		$context_path = $post_values['context_path'];
        
		dump($context_path);
		$values = array();
        
        dump($post_values);
//        exit;
        foreach ($post_values as $key => $value)
        {
            
            if (in_array($key, array(SurveyViewerForm :: FINISH_BUTTON, SurveyViewerForm :: NEXT_BUTTON, SurveyViewerForm :: BACK_BUTTON)))
            {
                $next_context_path = $value;
                if ($key == SurveyViewerForm :: FINISH_BUTTON)
                {
                   $this->finish_survey();
                }
            }
            
            $value = Security :: remove_XSS($value);
            $split_key = split('_', $key);
            $count = count($split_key);
            $complex_question_id = $split_key[0];
            
            if (is_numeric($complex_question_id))
            {
               if (!StringUtilities :: is_null_or_empty($value, true))
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
        
//        dump($complex_question_ids);
      
        if (count($complex_question_ids) > 0)
        {
            foreach ($complex_question_ids as $complex_question_id)
            {
                $answers = $values[$complex_question_id];
                
                if (count($answers) > 0)
                {
//                    dump($answers);
                	$this->survey_viewer->save_answer($complex_question_id, $answers, $context_path . '_' . $complex_question_id);
                }
            }
        }

           exit;
        return $next_context_path;
    }

    function finish_survey()
    {
       $this->get_survey_viewer()->finished();
    }

    /**
     * @return SurveyDisplaySurveyViewerComponent
     */
    function get_survey_viewer()
    {
        return $this->survey_viewer;
    }
    
}
?>