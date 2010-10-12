<?php
require_once dirname(__FILE__) . '/../survey_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../survey_manager/survey_manager.class.php';
require_once (dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php');
require_once (dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php');

class SurveyQuestionReportingBlock extends SurveyReportingBlock
{
    
    const NO_ANSWER = 'noAnswer';
    const COUNT = 'count';
    const TOTAL = 'total';
    
    private $question_id;
    private $question;

    
    function SurveyQuestionReportingBlock($parent, $question_id){
    	parent :: __construct($parent);
    	$this->question_id = $question_id;
    	$this->question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id); 
    }
    
    public function count_data()
    {
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
        $filter_parameters = $this->get_filter_parameters();
        $groups = $filter_parameters[SurveyReportingFilterWizard :: PARAM_GROUPS];
        $user_ids = array();
        if(count($groups)){
        	foreach ($groups as $group_id) {
        		$group = GroupDataManager::get_instance()->retrieve_group($group_id);
        		$group_user_ids = $group->get_users(true, true);
        		$user_ids = array_merge($user_ids, $group_user_ids);
        	}
        }
    	$user_ids = array_unique($user_ids);
    	$conditions = array();    	
    	$conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $this->question_id);
        if(count($user_ids)){
        	$conditions[] = new InCondition(SurveyQuestionAnswerTracker :: PROPERTY_USER_ID, $user_ids);
        }
    	
        $condition = new AndCondition($conditions);
        $trackers = Tracker :: get_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager::APPLICATION_NAME, $condition);
        
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
                $total_key = count($matches);
                $matches[] = Translation :: get(self :: COUNT);
                
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
                    //                    $answer_count[$option_count][$total_key] = 0;
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
                        $totals = array();
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
                            $answer_count[$key][$total_key] ++;
                        }
                    }
                }
                
                //creating actual reporing data
                

                foreach ($matches as $match)
                {
                    $reporting_data->add_row(strip_tags($match));
                }
                
                $totals = array();
                
                //like it was: = abdsolute figures
                //                foreach ($options as $option_key => $option)
                //                {
                //                    $reporting_data->add_category($option);
                //                    //                    dump('op key: '.$option_key);
                //                    //                    dump('option: '.$option);
                //                    foreach ($matches as $match_key => $match)
                //                    {
                //                        //                        dump('match key: '.$match_key);
                //                        //                    	dump('match: '.$match);
                //                        //                    	dump('answer_count: '.$answer_count[$option_key][$match_key]);
                //                        $totals[$match_key] = $totals[$match_key] + $answer_count[$option_key][$match_key];
                //                        //                        dump('total: '.$totals[$match_key]);
                //                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key][$match_key]);
                //                    }
                //                
                //                }
                

                //percentage figures 
                

                //total count
                
	
//                dump($answer_count);
              
                
                foreach ($options as $option_key => $option)
                {
                    
                    foreach ($matches as $match_key => $match)
                    {
                        
                        $totals[$match_key] = $totals[$match_key] + $answer_count[$option_key][$match_key];
                    
                    }
                    $totals[$match_key] = $totals[$match_key];
                		
                }
                
//                dump($totals);
                
//                  exit;
                
                $total_colums = count($totals);
                $total_count = $totals[$total_colums - 1];
                
                $summary_totals = array();
                
                $median_number = 1;
                
                foreach ($totals as $index => $value)
                {
                    if ($total_count == 0)
                    {
                        $summary_totals[$index] = 0;
                    }
                    else
                    {
                        //                        $percentage = number_format($value / $total_count * 100, 2);
                        //                        $summary_totals[$index] = $percentage;
                        $summary_totals[$index] = $value*$median_number;
                    }
                	$median_number++;
                }
                //                dump($totals);
                //                dump($summary_totals);
                //                exit;
                //set the actual percentages
                

                //                dump($answer_count);
                $match_count = count($matches);
                $total_index = $match_count - 1;
                //                dump($match_count);
                

                //                exit();
                
				
                foreach ($options as $option_key => $option)
                {
                    $reporting_data->add_category($option);
                    //                    dump('op key: '.$option_key);
                    //                    dump('option: '.$option);
                    $median_number = 1;
                    $median = 0;
                    $count = 0;
                    foreach ($matches as $match_key => $match)
                    {
                        //                        dump('match key: '.$match_key);
                        //                    	dump('match: '.$match);
                        //                    	dump('answer_count: '.$answer_count[$option_key][$match_key]);
                        //                        $totals[$match_key] = $totals[$match_key] + $answer_count[$option_key][$match_key];
                        //                        dump('total: '.$totals[$match_key]);
                        if ($match_key == $total_index)
                        {
                            //                        	$value = $answer_count[$option_key][$match_key] / $total_count;
                            //                            $percentage = number_format($value * 100, 2);
                            $count = $answer_count[$option_key][$total_index];
//                            dump($count);
//                            dump($match);
//                            $reporting_data->add_data_category_row($option, strip_tags($match), $percentage);
                        }
                        else
                        {
                            //                            $value = $answer_count[$option_key][$match_key] / $answer_count[$option_key][$total_index];
                            //                            $percentage = number_format($value * 100, 2);
                            $percentage = $answer_count[$option_key][$match_key] * $median_number;
                            $median = $median + $percentage;
                            $reporting_data->add_data_category_row($option, strip_tags($match), $percentage);
                        }
                       
                        
                        
                        $median_number ++;
                    }
                	 $median = $median/$count;
                	 $median = number_format($median, 2);
                    $reporting_data->add_data_category_row($option, Translation :: get(self :: COUNT), $median);
//                    dump($median);
                }
                
//                exit;
                
                //                dump($totals);
                //                
                //                dump($answer_count);
                

                //                dump($totals);
                

                //                exit;
                

                if (count($options) > 1)
                {
                    $reporting_data->add_category(Translation :: get(self :: TOTAL));
                    
                    //                    foreach ($options as $option)
                    //                    {
                    $total = 0;
                    foreach ($matches as $match_key => $match)
                    {
                        if($match != Translation :: get(self :: COUNT)){
                        	   
                        	$reporting_data->add_data_category_row(Translation :: get(self :: TOTAL), strip_tags($match), $summary_totals[$match_key]);
                        	$total =$total + $summary_totals[$match_key];
                        }
                    	
//                    	dump($match_key);
//                        dump($match);
//                    	dump($summary_totals[$match_key]);
                    }
//                    dump($total);
//                    dump($totals);
                    $keys =array_keys($matches, Translation :: get(self :: COUNT));
//                    dump($totals[$keys[0]]);
                    
                    $median = $total/$totals[$keys[0]];
                    $median = number_format($median, 2);
//                    dump($median);
                    $reporting_data->add_data_category_row(Translation :: get(self :: TOTAL), Translation :: get(self :: COUNT), $median);
                    
                    
                    //                    }
                

                }
//                exit;
                break;
            case SurveyMultipleChoiceQuestion :: get_type_name() :
                
                //get options and matches
                $opts = $this->question->get_options();
                foreach ($opts as $option)
                {
                    $options[] = $option->get_value();
                }
                //                $options[] = self :: NO_ANSWER;
                

                $matches[] = Translation :: get(self :: COUNT);
                
                //create answer matrix for answer counting
                

                $option_count = count($options) - 1;
                while ($option_count >= 0)
                {
                    $answer_count[$option_count] = 0;
                    $option_count --;
                }
                //                $answer_count[self :: NO_ANSWER] = 0;
                

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
                
                //totalcount
                $total_count = 0;
                foreach ($options as $option_key => $option)
                {
                    
                    foreach ($matches as $match)
                    {
                        $total_count = $total_count + $answer_count[$option_key];
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
                        $value = $answer_count[$option_key] / $total_count;
                        $percentage = number_format($value * 100, 2);
                        $reporting_data->add_data_category_row($option, strip_tags($match), $percentage);
                    }
                
                }
                if (count($options) > 1)
                {
                    $reporting_data->add_category(Translation :: get(self :: TOTAL));
                    foreach ($matches as $match)
                    {
                        $reporting_data->add_data_category_row(Translation :: get(self :: TOTAL), strip_tags($match), 100);
                    }
                }
                
                break;
            default :
                ;
                break;
        }
        
        return $reporting_data;
    }
    

    
    
    
    //old    
// switch ($type)
//        {
//            case SurveyMatrixQuestion :: get_type_name() :
//                
//                //get options and matches
//                $opts = $this->question->get_options();
//                foreach ($opts as $option)
//                {
//                    $options[] = $option->get_value();
//                }
//                
//                $matchs = $this->question->get_matches();
//                foreach ($matchs as $match)
//                {
//                    $matches[] = $match;
//                }
//                
//                //create answer matrix for answer counting
//                
//
//                $option_count = count($options) - 1;
//                
//                while ($option_count >= 0)
//                {
//                    $match_count = count($matches) - 1;
//                    while ($match_count >= 0)
//                    {
//                        $answer_count[$option_count][$match_count] = 0;
//                        $match_count --;
//                    }
//                    $answer_count[$option_count][self :: NO_ANSWER] = 0;
//                    $option_count --;
//                }
//                
//                //count answers from all answer trackers
//                
//
//                while ($tracker = $trackers->next_result())
//                {
//                    $answer = $tracker->get_answer();
//                    $options_answered = array();
//                    foreach ($answer as $key => $option)
//                    {
//                        $options_answered[] = $key;
//                        foreach ($option as $match_key => $match)
//                        {
//                            if ($this->question->get_matrix_type() == SurveyMatrixQuestion :: MATRIX_TYPE_CHECKBOX)
//                            {
//                                $answer_count[$key][$match_key] ++;
//                            }
//                            else
//                            {
//                                $answer_count[$key][$match] ++;
//                            }
//                        
//                        }
//                    }
//                    $all_options = array();
//                    foreach ($answer_count as $key => $option)
//                    {
//                        $all_options[] = $key;
//                    }
//                    $options_not_answered = array_diff($all_options, $options_answered);
//                    foreach ($options_not_answered as $option)
//                    {
//                        $answer_count[$option][self :: NO_ANSWER] ++;
//                    
//                    }
//                }
//                
//                //creating actual reporing data
//                
//
//                foreach ($matches as $match)
//                {
//                    $reporting_data->add_row(strip_tags($match));
//                }
//                
//                $reporting_data->add_row(self :: NO_ANSWER);
//                
//                foreach ($options as $option_key => $option)
//                {
//                    
//                    $reporting_data->add_category($option);
//                    
//                    foreach ($matches as $match_key => $match)
//                    {
//                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key][$match_key]);
//                    }
//                    $reporting_data->add_data_category_row($option, self :: NO_ANSWER, $answer_count[$option_key][self :: NO_ANSWER]);
//                
//                }
//                break;
//            case SurveyMultipleChoiceQuestion :: get_type_name() :
//                
//                //get options and matches
//                $opts = $this->question->get_options();
//                foreach ($opts as $option)
//                {
//                    $options[] = $option->get_value();
//                }
//                $options[] = self :: NO_ANSWER;
//                
//                $matches[] = self :: COUNT;
//                
//                //create answer matrix for answer counting
//                
//
//                $option_count = count($options) - 1;
//                while ($option_count >= 0)
//                {
//                    $answer_count[$option_count] = 0;
//                    $option_count --;
//                }
//                $answer_count[self :: NO_ANSWER] = 0;
//                
//                //count answers from all answer trackers
//                
//
//                while ($tracker = $trackers->next_result())
//                {
//                    $answer = $tracker->get_answer();
//                    foreach ($answer as $key => $option)
//                    {
//                        if ($this->question->get_answer_type() == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
//                        {
//                            $answer_count[$key] ++;
//                        }
//                        else
//                        {
//                            $answer_count[$option] ++;
//                        }
//                    }
//                }
//                
//                //creating actual reporing data
//                
//
//                foreach ($matches as $match)
//                {
//                    $reporting_data->add_row(strip_tags($match));
//                }
//                
//                foreach ($options as $option_key => $option)
//                {
//                    
//                    $reporting_data->add_category($option);
//                    
//                    foreach ($matches as $match)
//                    {
//                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key]);
//                    }
//                }
//                break;
//            default :
//                ;
//                break;
//        }
    
}

?>