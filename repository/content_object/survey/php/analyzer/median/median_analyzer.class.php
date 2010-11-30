<?php 
namespace repository\content_object\survey;

use reporting\ReportingData;
use repository\content_object\survey_matrix_question\SurveyMatrixQuestion;
use repository\content_object\survey_multiple_choice_question\SurveyMultipleChoiceQuestion;
use repository\content_object\survey_open_question\SurveyOpenQuestion;

class SurveyMedianAnalyzer extends SurveyAnalyzer
{
    
    const NO_ANSWER = 'noAnswer';
    const COUNT = 'count';
    const TOTAL = 'total';

    function analyse()
    {
        
        $question = $this->get_question();
        $type = $question->get_type();
        $answers = $this->get_answers();
        
        $reporting_data = new ReportingData();
        
        //option and matches of question
        $options = array();
        $matches = array();
        
        //matrix to store the answer count
        $answer_count = array();
        
        switch ($type)
        {
            case SurveyMatrixQuestion :: get_type_name() :
                
                //get options and matches
                $opts = $question->get_options();
                foreach ($opts as $option)
                {
                    $options[] = $option->get_value();
                }
                
                $matchs = $question->get_matches();
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
                    $option_count --;
                }
                
                //count answers from all answer trackers
                foreach ($answers as $answer)
                {
                    
                    $options_answered = array();
                    foreach ($answer as $key => $option)
                    {
                        $options_answered[] = $key;
                        $totals = array();
                        foreach ($option as $match_key => $match)
                        {
                            if ($question->get_matrix_type() == SurveyMatrixQuestion :: MATRIX_TYPE_CHECKBOX)
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
        
                foreach ($options as $option_key => $option)
                {
                    
                    foreach ($matches as $match_key => $match)
                    {
                        
                        $totals[$match_key] = $totals[$match_key] + $answer_count[$option_key][$match_key];
                    
                    }
                    $totals[$match_key] = $totals[$match_key];
                
                }

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
                       $summary_totals[$index] = $value * $median_number;
                    }
                    $median_number ++;
                }
               
                $match_count = count($matches);
                $total_index = $match_count - 1;
                              

                foreach ($options as $option_key => $option)
                {
                    $reporting_data->add_category($option);
                  
                    $median_number = 1;
                    $median = 0;
                    $count = 0;
                    foreach ($matches as $match_key => $match)
                    {
                       
                        if ($match_key == $total_index)
                        {
                          
                            $count = $answer_count[$option_key][$total_index];
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
                    $median = $median / $count;
                    $median = number_format($median, 2);
                    $reporting_data->add_data_category_row($option, Translation :: get(self :: COUNT), $median);
                }
                
                if (count($options) > 1)
                {
                    $reporting_data->add_category(Translation :: get(self :: TOTAL));
                
                    $total = 0;
                    foreach ($matches as $match_key => $match)
                    {
                        if ($match != Translation :: get(self :: COUNT))
                        {
                            
                            $reporting_data->add_data_category_row(Translation :: get(self :: TOTAL), strip_tags($match), $summary_totals[$match_key]);
                            $total = $total + $summary_totals[$match_key];
                        }
                  
                    }
                    $keys = array_keys($matches, Translation :: get(self :: COUNT));

                    $median = $total / $totals[$keys[0]];
                    $median = number_format($median, 2);
                    $reporting_data->add_data_category_row(Translation :: get(self :: TOTAL), Translation :: get(self :: COUNT), $median);

                }
                break;
            case SurveyMultipleChoiceQuestion :: get_type_name() :
                
                //get options and matches
                $opts = $question->get_options();
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
        
                //count answers from all answer trackers

                foreach ($answers as $answer)
                {
                    foreach ($answer as $key => $option)
                    {
                        if ($question->get_answer_type() == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
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
            case SurveyOpenQuestion :: get_type_name() :
                $reporting_data->add_category('answer');
                $stripped_answers = array();
                foreach ($answers as $answer)
                {
                    if (strlen(strip_tags($answer[0], '<img>')) > 0)
                    {
                        $stripped_answers[] = $answer[0];
                    }
                }
                
                $answer_count = count($stripped_answers);
                
                $categories = array();
                $nr = 0;
                while ($answer_count > 0)
                {
                    $nr ++;
                    $categories[] = $nr;
                    $answer_count --;
                }
                
                $answer_row = Translation :: get('Answer');
                $rows = array($answer_row);
                
                $reporting_data->set_categories($categories);
                $reporting_data->set_rows($rows);
                $nr = 0;
                foreach ($stripped_answers as $answer)
                {
                    $nr ++;
                    $reporting_data->add_data_category_row($nr, $answer_row, $answer);
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