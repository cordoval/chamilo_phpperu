<?php
require_once dirname(__FILE__) . '/../survey_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../survey_manager/survey_manager.class.php';
require_once (dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php');
require_once (dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php');

class SurveyQuestionReportingBlock extends SurveyReportingBlock
{
    
    const NO_ANSWER = 'noAnswer';
    const COUNT = 'count';
    
    private $question;

    public function count_data()
    {
        
        $question_id = $this->get_survey_question_id();
        
//        dump($question_id);
//        
//        //        $condition = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $this->get_survey_question_id());
//        $tracker = new SurveyQuestionAnswerTracker();
//        $count = $tracker->count_tracker_items($condition);
//        dump($count);
//        $trackers = $tracker->retrieve_tracker_items_result_set($condition);
//        $questions_ids = array();
//        while ($tracker = $trackers->next_result())
//        {
//            $questions_ids[] = $tracker->get_question_cid();
//        }
//        dump(count(array_unique($questions_ids)));
//        
//        exit();
        
        $this->question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);
        
        return $this->create_reporting_data();
    
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }

    private function create_reporting_data()
    {
        
        //retrieve the answer trackers
        $condition = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $this->get_survey_question_id());
        $tracker = new SurveyQuestionAnswerTracker();
        $trackers = $tracker->retrieve_tracker_items_result_set($condition);
        
        //option and matches of question
        $options = array();
        $matches = array();
        
        //matrix to store the answer count
        $answer_count = array();
        
        //reproting data and type of question
        $reporting_data = new ReportingData();
        $type = $this->question->get_type();
        
        switch ($type)
        {
            case SurveyMatrixQuestion :: get_type_name() :
                
                //get options and matches
                $opts = $this->question->get_options();
                foreach ($opts as $option)
                {
                    $options[] = $option->get_value();
                }
                
                $matchs = $this->question->get_matches();
                foreach ($matchs as $match)
                {
                    $matches[] = $match;
                }
                
                //create answer matrix for answer counting
                

                $option_count = count($options) - 1;
                
                while ($option_count >= 0)
                {
                    $match_count = count($matches) - 1;
                    while ($match_count >= 0)
                    {
                        $answer_count[$option_count][$match_count] = 0;
                        $match_count --;
                    }
                    $answer_count[$option_count][self :: NO_ANSWER] = 0;
                    $option_count --;
                }
                
                //count answers from all answer trackers
                

                while ($tracker = $trackers->next_result())
                {
                    $answer = $tracker->get_answer();
                    $options_answered = array();
                    foreach ($answer as $key => $option)
                    {
                        $options_answered[] = $key;
                        foreach ($option as $match_key => $match)
                        {
                            if ($this->question->get_matrix_type() == SurveyMatrixQuestion :: MATRIX_TYPE_CHECKBOX)
                            {
                                $answer_count[$key][$match_key] ++;
                            }
                            else
                            {
                                $answer_count[$key][$match] ++;
                            }
                        
                        }
                    }
                    $all_options = array();
                    foreach ($answer_count as $key => $option)
                    {
                        $all_options[] = $key;
                    }
                    $options_not_answered = array_diff($all_options, $options_answered);
                    foreach ($options_not_answered as $option)
                    {
                        $answer_count[$option][self :: NO_ANSWER] ++;
                    
                    }
                }
                
                //creating actual reporing data
                

                foreach ($matches as $match)
                {
                    $reporting_data->add_row(strip_tags($match));
                }
                
                $reporting_data->add_row(self :: NO_ANSWER);
                
                foreach ($options as $option_key => $option)
                {
                    
                    $reporting_data->add_category($option);
                    
                    foreach ($matches as $match_key => $match)
                    {
                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key][$match_key]);
                    }
                    $reporting_data->add_data_category_row($option, self :: NO_ANSWER, $answer_count[$option_key][self :: NO_ANSWER]);
                
                }
                break;
            case SurveyMultipleChoiceQuestion :: get_type_name() :
                
                //get options and matches
                $opts = $this->question->get_options();
                foreach ($opts as $option)
                {
                    $options[] = $option->get_value();
                }
                $options[] = self :: NO_ANSWER;
                
                $matches[] = self :: COUNT;
                
                //create answer matrix for answer counting
                

                $option_count = count($options) - 1;
                while ($option_count >= 0)
                {
                    $answer_count[$option_count] = 0;
                    $option_count --;
                }
                $answer_count[self :: NO_ANSWER] = 0;
                
                //count answers from all answer trackers
                

                while ($tracker = $trackers->next_result())
                {
                    $answer = $tracker->get_answer();
                    foreach ($answer as $key => $option)
                    {
                        if ($this->question->get_answer_type() == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
                        {
                            $answer_count[$key] ++;
                        }
                        else
                        {
                            $answer_count[$option] ++;
                        }
                    }
                }
                
                //creating actual reporing data
                

                foreach ($matches as $match)
                {
                    $reporting_data->add_row(strip_tags($match));
                }
                
                foreach ($options as $option_key => $option)
                {
                    
                    $reporting_data->add_category($option);
                    
                    foreach ($matches as $match)
                    {
                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key]);
                    }
                }
                break;
            default :
                ;
                break;
        }
        
        return $reporting_data;
    }
}

?>