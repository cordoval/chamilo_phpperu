<?php
require_once (Path :: get_reporting_path() . 'lib/reporting_data.class.php');
require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class SurveyManagerSurveyExcelExporterComponent extends SurveyManager
{
    
    const COUNT = 'count';
    const TOTAL = 'total';

    /**
     * Runs this component and displays its output.
     */
    
    function run()
    {
        $survey_publication_id = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_publication_id);
        $this->render_data($survey_publication);
    
    }

    public function render_data($survey_publication)
    {
        
        $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $survey_publication->get_id());
        $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        $participants_ids = array();
        while ($tracker = $trackers->next_result())
        {
            $participants_ids[] = $tracker->get_id();
        }
        
        $excel = new PHPExcel();
        
        $worksheet = $excel->getSheet(0)->setTitle('Algemeen');
        $this->render_summary_data($worksheet, $survey_publication, $participants_ids);
        
        //        $survey = $survey_publication->get_publication_object();
        //        $pages = $survey->get_pages();
        //        
        //        
        //        $page_index = 1;
        //        foreach ($pages as $page)
        //        {
        //            if ($page->count_questions() != 0)
        //            {
        //                $title = Utilities :: truncate_string(trim(strip_tags($page->get_title())), 20, true, '');
        //                $description = Utilities :: truncate_string(trim(strip_tags($page->get_description())), 20, true, '');
        //                $worksheet = $excel->createSheet($page_index)->setTitle($title);
        //                
        //                $questions = $page->get_questions();
        //                
        //                $reporting_page_data = array();
        //                
        //                foreach ($questions as $question)
        //                {
        //                    $reporting_data = $this->create_reporting_data($question, $participants_ids);
        //                    
        //                    $reporting_data_question = array();
        //                    $reporting_data_question[] = $question->get_title();
        //                    $reporting_data_question[] = $question->get_description();
        //                    $reporting_data_question[] = $reporting_data;
        //                    $reporting_page_data[] = $reporting_data_question;
        //                }
        //                $this->render_page_data($worksheet, $reporting_page_data);
        //            }
        //            $page_index ++;
        //        }
        

        //        exit();
        

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->get_file_name() . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory :: createWriter($excel, 'Excel2007');
        return $objWriter->save('php://output');
        
        $excel->disconnectWorksheets();
        unset($excel);
    
    }

    private function get_file_name()
    {
        $survey_publication_id = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_publication_id);
        $survey = $survey_publication->get_publication_object();
        return $survey->get_title();
    }

    public function render_summary_data($worksheet, $survey_publication, $participant_trackers)
    {
        
        $condition = new EqualityCondition(SurveyPublicationGroup :: PROPERTY_SURVEY_PUBLICATION, $survey_publication->get_id());
        $publication_rel_groups = SurveyDataManager :: get_instance()->retrieve_survey_publication_groups();
        
        $groups = array();
        $user_ids = array();
        $total_user_ids = array();
        while ($publication_rel_group = $publication_rel_groups->next_result())
        {
            $group = GroupDataManager :: get_instance()->retrieve_group($publication_rel_group->get_group_id());
            $groups[] = $group;
            $user_ids[$group->get_id()] = $group->get_users(true, true);
            $total_user_ids = array_merge($total_user_ids, $user_ids[$group->get_id()]);
        }
        
        $total_user_ids = array_unique($total_user_ids);
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $survey_publication->get_id());
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_STATUS, SurveyParticipantTracker :: STATUS_NOTSTARTED);
        $condition = new AndCondition($conditions);
        $not_started_trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        $not_started_participants_ids = array();
        while ($not_started_tracker = $not_started_trackers->next_result())
        {
            $not_started_participants_ids[] = $not_started_tracker->get_id();
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $survey_publication->get_id());
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_STATUS, array(SurveyParticipantTracker :: STATUS_STARTED, SurveyParticipantTracker :: STATUS_FINISHED));
        $condition = new AndCondition($conditions);
        $started_trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        $started_participants_ids = array();
        while ($started_tracker = $started_trackers->next_result())
        {
            $started_participants_ids[] = $started_tracker->get_id();
        }
        
        $column = 1;
        $row = 1;
        
        $worksheet->getColumnDimensionByColumn(0)->setWidth(20);
        $worksheet->getColumnDimensionByColumn(1)->setWidth(40);
        $worksheet->getColumnDimensionByColumn(2)->setWidth(20);
        
        $survey_title = $survey_publication->get_publication_object()->get_title();
        
        $worksheet->setCellValueByColumnAndRow($column - 1, $row, $survey_title);
        $this->wrap_text($worksheet, $column, $row);
        
        $row ++;
        $total = count($total_user_ids);
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Aantal genodigde');
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, $total);
        $row ++;
        $started = count($started_participants_ids);
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Aantal participanten');
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, $started);
        $row ++;
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Niet deelgenomen');
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, count($not_started_participants_ids));
        $row ++;
        $participatie = $started / $total * 100;
        $participatie = number_format($participatie, 2);
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Participatigraad (%)');
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, $participatie);
        $row= $row+2;
        
        $worksheet->setCellValueByColumnAndRow($column-1, $row, 'Participatie (%)');
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Groepen');
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, 'Omschrijving');
        $row ++;
        
        foreach ($groups as $group)
        {
            $name = $group->get_name();
            $description = $group->get_description();
            $worksheet->setCellValueByColumnAndRow($column, $row, $name);
            $worksheet->setCellValueByColumnAndRow($column + 1, $row, $description);
            $row ++;
        }
    
    }

    public function render_page_data($worksheet, $data)
    {
        
        $column = 0;
        $block_row = 0;
        
        $worksheet->getColumnDimensionByColumn($column)->setWidth(50);
        
        if (is_array($data))
        {
            
            foreach ($data as $block_data)
            {
                $column = 0;
                $block_row = $block_row + 2;
                
                $block_title = trim(html_entity_decode(strip_tags($block_data[0])));
                $block_description = trim(html_entity_decode(strip_tags($block_data[1])));
                $block_content_data = $block_data[2];
                
                $worksheet->setCellValueByColumnAndRow($column, $block_row, $block_title);
                $this->wrap_text($worksheet, $column, $block_row);
                
                if ($block_description != '')
                {
                    $block_row ++;
                    $worksheet->setCellValueByColumnAndRow($column, $block_row, $block_description);
                    $this->wrap_text($worksheet, $column, $block_row);
                    $block_row ++;
                }
                
                $block_row ++;
                
                foreach ($block_content_data->get_rows() as $row_id => $row_name)
                {
                    //	dump($row_name);
                    $column ++;
                    $worksheet->getColumnDimensionByColumn($column)->setAutoSize(true);
                    $worksheet->setCellValueByColumnAndRow($column, $block_row, trim(html_entity_decode(strip_tags($row_name))));
                    $this->wrap_text($worksheet, $column, $block_row);
                
                }
                
                $block_row ++;
                
                foreach ($block_content_data->get_categories() as $category_id => $category_name)
                {
                    $column = 0;
                    $worksheet->setCellValueByColumnAndRow($column, $block_row, trim(html_entity_decode(strip_tags($category_name))));
                    $this->wrap_text($worksheet, $column, $block_row);
                    
                    foreach ($block_content_data->get_rows() as $row_id => $row_name)
                    {
                        $column ++;
                        $worksheet->setCellValueByColumnAndRow($column, $block_row, $block_content_data->get_data_category_row($category_id, $row_id));
                    }
                    $block_row ++;
                }
            
            }
        
        }
    
    }

    function wrap_text($worksheet, $colum, $row)
    {
        $worksheet->getStyleByColumnAndRow($colum, $row)->getAlignment()->setWrapText(true);
    
    }

    private function create_reporting_data($question, $participant_ids)
    {
        
        //retrieve the answer trackers
        $conditions = array();
        $conditions[] = new InCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $participant_ids);
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $question->get_id());
        $condition = new AndCondition($conditions);
        $trackers = Tracker :: get_data('survey_question_answer_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        
        //option and matches of question
        $options = array();
        $matches = array();
        
        //matrix to store the answer count
        $answer_count = array();
        
        //reporting data and type of question
        $reporting_data = new ReportingData();
        $type = $question->get_type();
        
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
                $matches[] = Translation :: get(self :: TOTAL);
                
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
                
                foreach ($options as $option_key => $option)
                {
                    $reporting_data->add_category($option);
                    
                    foreach ($matches as $match_key => $match)
                    {
                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key][$match_key]);
                    
                    }
                
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
                //                $answer_count[self :: NO_ANSWER] = 0;
                

                //count answers from all answer trackers
                

                while ($tracker = $trackers->next_result())
                {
                    $answer = $tracker->get_answer();
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
                
                $total = 0;
                foreach ($options as $option_key => $option)
                {
                    
                    $reporting_data->add_category($option);
                    foreach ($matches as $match)
                    {
                        $total = $total + $answer_count[$option_key];
                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key]);
                    }
                
                }
                $reporting_data->add_category('Total');
                foreach ($matches as $match)
                {
                    //                    $reporting_data->add_row(strip_tags($match));
                    $reporting_data->add_data_category_row('Total', strip_tags($match), $total);
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