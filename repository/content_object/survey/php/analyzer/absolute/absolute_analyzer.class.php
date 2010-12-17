<?php 
namespace repository\content_object\survey;

use reporting\ReportingData;
use repository\content_object\survey_matrix_question\SurveyMatrixQuestion;
use repository\content_object\survey_multiple_choice_question\SurveyMultipleChoiceQuestion;
use repository\content_object\survey_open_question\SurveyOpenQuestion;
use repository\content_object\survey_select_question\SurveySelectQuestion;
use common\libraries\Translation;

class SurveyAbsoluteAnalyzer extends SurveyAnalyzer
{
 const NO_ANSWER = 'noAnswer';
    const COUNT = 'count';
    const TOTAL = 'total';

    function analyse()
    {
        
        $question = $this->get_question();
        $type = $question->get_type();
        $reporting_data = new ReportingData();
        
        $answers = $this->get_answers();
        
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
                    //                    $answer_count[$option_count][self :: NO_ANSWER] = 0;
                    $option_count --;
                }
                
                //count answers                 
                

                foreach ($answers as $answer)
                {
                    $options_answered = array();
                    foreach ($answer as $key => $option)
                    {
                        $options_answered[] = $key;
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
                        
                        }
                    }
                    $all_options = array();
                    foreach ($answer_count as $key => $option)
                    {
                        $all_options[] = $key;
                    }
                    $options_not_answered = array_diff($all_options, $options_answered);
                
     //                    foreach ($options_not_answered as $option)
                //                    {
                //                        $answer_count[$option][self :: NO_ANSWER] ++;
                //                    
                //                    }
                }
                
                //creating actual reporing data
                

                foreach ($matches as $match)
                {
                    $reporting_data->add_row(strip_tags($match));
                }
                
                //                $reporting_data->add_row(self :: NO_ANSWER);
                

                foreach ($options as $option_key => $option)
                {
                    
                    $reporting_data->add_category($option);
                    
                    foreach ($matches as $match_key => $match)
                    {
                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key][$match_key]);
                    }
                
     //                    $reporting_data->add_data_category_row($option, self :: NO_ANSWER, $answer_count[$option_key][self :: NO_ANSWER]);
                

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
                

                $matches[] = self :: COUNT;
                
                //create answer matrix for answer counting
                

                $option_count = count($options) - 1;
                while ($option_count >= 0)
                {
                    $answer_count[$option_count] = 0;
                    $option_count --;
                }
                //                $answer_count[self :: NO_ANSWER] = 0;
                

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
            case SurveyOpenQuestion :: get_type_name() :
                $reporting_data->add_category('answer');
                $stripped_answers = array();
                //                foreach ($answers as $answer)
                //                {
                ////                	dump(html_entity_decode($answer[0], ENT_QUOTES, 'UTF-8'));
                //                	$stripped_answer = trim(strip_tags(html_entity_decode($answer[0], ENT_QUOTES, 'UTF-8')));
                //                	$stripped_answer = str_replace(html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8'), ' ', $stripped_answer);
                //                	$stripped_answer = preg_replace('/[ \n\r\t]{2,}/', ' ', $stripped_answer);
                //                	
                //                    if (strlen($stripped_answer) > 0)
                //                    {
                ////                    	dump($stripped_answer);
                ////                    	dump(html_entity_decode(Text :: convert_character_set($stripped_answer, 'UTF-8', 'ISO-8859-1'), ENT_QUOTES, 'ISO-8859-1'));
                //                        $stripped_answers[] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $stripped_answer);
                //                    }
                //                }
                

                foreach ($answers as $answer)
                {
                    if (strlen(strip_tags($answer[0])) > 0)
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
                
                //                dump($stripped_answers);
                

                foreach ($stripped_answers as $answer)
                {
                    $nr ++;
                    $reporting_data->add_data_category_row($nr, $answer_row, $answer);
                }
                break;
            case SurveySelectQuestion :: get_type_name() :
                              
                $opts = $question->get_options();
                foreach ($opts as $option)
                {
                    $options[] = $option->get_value();
                }
                
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
                
                $answer_row = Translation :: get(self :: COUNT);
                $rows = array($answer_row);
                
                $reporting_data->set_categories($options);
                $reporting_data->set_rows($rows);
                
                foreach ($options as $option_key => $option)
                {
                    
                    if ($answer_count[$option_key])
                    {
                        $reporting_data->add_data_category_row($option, $answer_row, $answer_count[$option_key]);
                    }
                    else
                    {
                        $reporting_data->add_data_category_row($option, $answer_row, 0);
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