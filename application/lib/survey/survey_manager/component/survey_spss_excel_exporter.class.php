<?php
require_once Path :: get_application_path() . 'lib/survey/survey_publication_group.class.php';
Path :: get_application_path() . 'lib/survey/survey_publication_user.class.php';
require_once (Path :: get_reporting_path() . 'lib/reporting_data.class.php');
require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class SurveyManagerSurveySpssExcelExporterComponent extends SurveyManager
{
    
    const COUNT = 'count';
    const TOTAL = 'total';
    const NOT_STARTED_PARTICIPANTS = 'not_started_participants';
    const STARTED_PARTICIPANTS = 'started_participants';
    const ALL_PARTICIPANTS = 'all_participants';
    const NOT_STARTED_PARTICIPANT_COUNT = 'not_started_participant_count';
    const STARTED_PARTICIPANT_COUNT = 'started_participant_count';
    const ALL_PARTICIPANT_COUNT = 'all_participant_count';
    const GROUPS = 'groups';
    const GROUP_NAME = 'group_name';
    const GROUP_DESCRIPTION = 'group_description';
    const INDIVIDUAL_USERS = 'individual_users';
    const USERS = 'users';
    const PARTICIPATION_GRADE = 'participation_grade';
    const SURVEYS = 'surveys';
    const SURVEY_NAME = 'survey_name';
    const SURVEY_DESCRIPTION = 'survey_description';
    const SURVEY_COUNT = 'survey_count';
    const REPORTING_DATA = 'reporting_data';
    const DATA_NAME = 'data_name';
    const DATA_DESCRIPTION = 'data_description';
    const DATA_GROUP = 'data_group';
    
    const VARIABLE_QUESTION_ID = 'question_id';
    const VARIABLE_NR = 'nr';
    const VARIABLE_NAME = 'name';
    const VARIABLE_TYPE = 'type';
    const VARIABLE_DESCRIPTION = 'description';
    const VARIABLE_LEVEL_OF_MEASUREMENT = 'level_of_measurement';
    const VARIABLE_MISSING_VALUE = 'missing_value';
    const VARIABLE_ANSWER = 'answer';
    const VARIABLE_CODE = 'code';
    const VARIABLE_LABEL = 'label';
    
    const SCALE_NOMINAL = 'nominal';
    const SCALE_ORDINAL = 'ordinal';
    const SCALE_INTERVAL = 'interval';
    const SCALE_RATIO = 'ratio';
    
    private $participants;
    private $surveys;
    private $variable_encodings;

    /**
     * Runs this component and displays its output.
     */
    
    function run()
    {
        $ids = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        
        if (! is_array($ids))
        {
            $ids = array($ids);
        }
        $this->create_participants($ids);
        
        $this->render_data();
    
    }

    public function render_data()
    {
        $excel = new PHPExcel();
        
        $worksheet = $excel->getSheet(0)->setTitle('Algemeen');
        
        $this->create_variable_encoding($worksheet);
        
        $this->render_summary_data($worksheet);
        
        $questions = $this->get_questions();
        $worksheet_index = 1;
        
        foreach ($questions as $question_id => $question)
        {
            
            $title = Utilities :: truncate_string(trim(strip_tags($question->get_title())), 20, true, '');
            //            $title = $title . ' (id:' . $question_id . ')';
            //            $worksheet = $excel->createSheet($worksheet_index)->setTitle($title);
            $worksheet = $excel->createSheet($worksheet_index)->setTitle($title);
            $worksheet = $excel->getSheet($worksheet_index);
            $page_reporting_data = $this->create_page_reporting_data($question);
            $this->render_page_data($worksheet, $page_reporting_data);
            $worksheet_index ++;
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: 



attachment;filename="' . 'survey_export' . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory :: createWriter($excel, 'Excel2007');
        return $objWriter->save('php://output');
    }

    private 

    function create_variable_encoding($worksheet)
    {
        
        $this->variable_encodings = array();
        
        $questions = $this->get_questions();
        $question_nr = 1;
        $var_index = 1;
        foreach ($questions as $question_id => $question)
        {
            
            $variable_name = 'Var ';
            
            $type = $question->get_type();
            
            switch ($type)
            {
                case SurveyMatrixQuestion :: get_type_name() :
                    
                    $opts = $question->get_options();
                    $options = array();
                    
                    foreach ($opts as $option)
                    {
                        $variable_encoding = array();
                        
                        $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
                        $var_index ++;
                        
                        $name = $option->get_value();
                        
                        $variable_encoding[self :: VARIABLE_QUESTION_ID] = $question_id;
                        $variable_encoding[self :: VARIABLE_DESCRIPTION] = $name;
                        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
                        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
                        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = 99;
                        $variable_encoding[self :: VARIABLE_NR] = $question_nr;
                        $question_nr ++;
                        
                        $matchs = $question->get_matches();
                        $matches = array();
                        foreach ($matchs as $match)
                        {
                            $matches[] = $match;
                        }
                        
                        $variable_encoding[self :: VARIABLE_LABEL] = $matches;
                        $this->variable_encodings[] = $variable_encoding;
                    
                    }
                    
                    break;
                case SurveyMultipleChoiceQuestion :: get_type_name() :
                    
                    $variable_encoding = array();
                    
                    $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
                    $var_index ++;
                    $variable_encoding[self :: VARIABLE_QUESTION_ID] = $question_id;
                    $variable_encoding[self :: VARIABLE_DESCRIPTION] = $question->get_title();
                    $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
                    $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
                    $variable_encoding[self :: VARIABLE_MISSING_VALUE] = 99;
                    $variable_encoding[self :: VARIABLE_NR] = $question_nr;
                    $question_nr ++;
                    
                    $opts = $question->get_options();
                    $options = array();
                    foreach ($opts as $option)
                    {
                        $options[] = $option->get_value();
                    }
                    
                    $variable_encoding[self :: VARIABLE_LABEL] = $options;
                    
                    $this->variable_encodings[] = $variable_encoding;
                    
                    break;
            }
        
        }
        
        dump($this->variable_encodings);
        exit();
    }

    private function create_raw_data_set()
    {
        
        $participants = $this->participants[self :: STARTED_PARTICIPANTS];
        $questions = $this->get_questions();
        
        foreach ($participants as $$participant_id)
        {
            
            foreach ($questions as $question)
            {
                
                $question_id = $question->get_id();
                $conditions = array();
                $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $participant_id);
                $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $question->get_id());
                $condition = new AndCondition($conditions);
                $tracker_count = Tracker :: count_data('survey_question_answer_tracker', SurveyManager :: APPLICATION_NAME, $condition);
                
                if ($tracker_count == 1)
                {
                    $trackers = Tracker :: get_data('survey_question_answer_tracker', SurveyManager :: APPLICATION_NAME, $condition);
                    $tracker = $trackers->next_result();
                }
                else
                {
                
                }
            
            }
        
        }
    
    }

    private function get_questions()
    {
        
        $page_questions = array();
        $surveys = $this->surveys;
        
        foreach ($surveys as $survey)
        {
            $pages = $survey->get_pages();
            foreach ($pages as $page)
            {
                if ($page->count_questions() != 0)
                {
                    $questions = $page->get_questions();
                    
                    foreach ($questions as $question)
                    {
                        
                        $page_questions[$question->get_id()] = $question;
                    }
                }
            }
        }
        
        return $page_questions;
    
    }

    private function render_summary_data($worksheet)
    {
        
        $column = 1;
        $row = 3;
        
        $worksheet->getColumnDimensionByColumn($column)->setWidth(20);
        $worksheet->getColumnDimensionByColumn($column + 1)->setWidth(20);
        $worksheet->getColumnDimensionByColumn($column + 2)->setWidth(20);
        $worksheet->getColumnDimensionByColumn($column + 3)->setWidth(20);
        $worksheet->getColumnDimensionByColumn($column + 4)->setWidth(5);
        $worksheet->getColumnDimensionByColumn($column + 5)->setWidth(200);
        
        $surveys = $this->participants[self :: SURVEYS];
        
        $worksheet->setCellValueByColumnAndRow($column, $row, Translation :: get('SurveyName'));
        $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, Translation :: get('SurveyDescription'));
        $worksheet->getStyleByColumnAndRow($column + 1, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column + 1, $row)->getFont()->setBold(true);
        $row ++;
        
        foreach ($surveys as $survey)
        {
            $title = $survey[self :: SURVEY_NAME];
            $worksheet->setCellValueByColumnAndRow($column, $row, $title);
            $this->wrap_text($worksheet, $column, $row);
            $description = $survey[self :: SURVEY_DESCRIPTION];
            $worksheet->setCellValueByColumnAndRow($column + 1, $row, $description);
            $this->wrap_text($worksheet, $column, $row);
            $row ++;
        }
        
        $row = $row + 2;
        $all_participants = $this->participants[self :: ALL_PARTICIPANT_COUNT];
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Aantal participanten');
        $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
        
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, $all_participants);
        $row ++;
        $started = $this->participants[self :: STARTED_PARTICIPANT_COUNT];
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Deelgenomen');
        $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
        
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, $started);
        $row ++;
        $not_started = $this->participants[self :: NOT_STARTED_PARTICIPANT_COUNT];
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Niet deelgenomen');
        $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
        
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, $not_started);
        $row ++;
        $participatie = $this->participants[self :: PARTICIPATION_GRADE];
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Participatigraad (%)');
        $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
        
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, $participatie);
        $row = $row + 2;
        
        $worksheet->setCellValueByColumnAndRow($column, $row, 'Aantal participanten');
        $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
        $worksheet->setCellValueByColumnAndRow($column + 1, $row, 'Deelgenomen');
        $worksheet->getStyleByColumnAndRow($column + 1, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column + 1, $row)->getFont()->setBold(true);
        $worksheet->setCellValueByColumnAndRow($column + 2, $row, 'Niet deelgenomen');
        $worksheet->getStyleByColumnAndRow($column + 2, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column + 2, $row)->getFont()->setBold(true);
        $worksheet->setCellValueByColumnAndRow($column + 3, $row, 'Participatie (%)');
        $worksheet->getStyleByColumnAndRow($column + 3, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column + 3, $row)->getFont()->setBold(true);
        $worksheet->setCellValueByColumnAndRow($column + 5, $row, 'Groepen');
        $worksheet->getStyleByColumnAndRow($column + 5, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
        $worksheet->getStyleByColumnAndRow($column + 5, $row)->getFont()->setBold(true);
        
        $row = $row + 2;
        
        $groups = $this->participants[self :: GROUPS];
        
        foreach ($groups as $group_id => $group)
        {
            //            $name = $this->participants[self :: GROUPS][$group_id][self :: GROUP_NAME];
            

            $all_participant_count = $group[self :: ALL_PARTICIPANT_COUNT];
            $worksheet->setCellValueByColumnAndRow($column, $row, $all_participant_count);
            
            $started_participant_count = $group[self :: STARTED_PARTICIPANT_COUNT];
            $worksheet->setCellValueByColumnAndRow($column + 1, $row, $started_participant_count);
            
            $not_started_participant_count = $group[self :: NOT_STARTED_PARTICIPANT_COUNT];
            $worksheet->setCellValueByColumnAndRow($column + 2, $row, $not_started_participant_count);
            
            $participatie = $group[self :: PARTICIPATION_GRADE];
            $worksheet->setCellValueByColumnAndRow($column + 3, $row, $participatie);
            
            $description = $group[self :: GROUP_DESCRIPTION];
            $worksheet->setCellValueByColumnAndRow($column + 5, $row, $description);
            $row ++;
        }
    
    }

    private function render_page_data($worksheet, $data)
    {
        
        $column = 0;
        $block_row = 0;
        
        $worksheet->getColumnDimensionByColumn($column)->setWidth(50);
        $column_count = 1;
        while ($column_count < 7)
        {
            $worksheet->getColumnDimensionByColumn($column + $column_count)->setWidth(15);
            $column_count ++;
        }
        
        if (is_array($data))
        {
            
            foreach ($data as $block_data)
            {
                $column = 0;
                $block_row = $block_row + 2;
                
                $participant_group = $block_data[self :: DATA_GROUP];
                $participant_count = $block_data[self :: STARTED_PARTICIPANT_COUNT];
                $block_title = trim(html_entity_decode(strip_tags($block_data[self :: DATA_NAME])));
                $block_description = trim(html_entity_decode(strip_tags($block_data[self :: DATA_DESCRIPTION])));
                $block_content_data = $block_data[self :: REPORTING_DATA];
                
                $worksheet->setCellValueByColumnAndRow($column, $block_row, $participant_group);
                $worksheet->getStyleByColumnAndRow($column, $block_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
                $worksheet->getStyleByColumnAndRow($column, $block_row)->getFont()->setBold(true);
                $this->wrap_text($worksheet, $column, $block_row);
                
                $block_row ++;
                $worksheet->setCellValueByColumnAndRow($column, $block_row, 'Deelnemers');
                $worksheet->setCellValueByColumnAndRow($column + 1, $block_row, $participant_count);
                $worksheet->getStyleByColumnAndRow($column + 1, $block_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
                $worksheet->getStyleByColumnAndRow($column + 1, $block_row)->getFont()->setBold(true);
                
                $block_row = $block_row + 2;
                
                $worksheet->setCellValueByColumnAndRow($column, $block_row, $block_title);
                $worksheet->getStyleByColumnAndRow($column, $block_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
                $worksheet->getStyleByColumnAndRow($column, $block_row)->getFont()->setBold(true);
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
                    $worksheet->getStyleByColumnAndRow($column, $block_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_CENTER);
                    $worksheet->getStyleByColumnAndRow($column, $block_row)->getFont()->setBold(true);
                    $worksheet->setCellValueByColumnAndRow($column, $block_row, trim(html_entity_decode(strip_tags($row_name), ENT_QUOTES)));
                    $this->wrap_text($worksheet, $column, $block_row);
                
                }
                
                $block_row ++;
                
                $row_count = count($block_content_data->get_rows());
                $category_count = count($block_content_data->get_categories());
                
                $categrory_row_index = 1;
                
                //                dump('row count: ' . $row_count);
                //                dump('cat count: ' . $category_count);
                

                foreach ($block_content_data->get_categories() as $category_id => $category_name)
                {
                    $column = 0;
                    
                    $worksheet->setCellValueByColumnAndRow($column, $block_row, trim(html_entity_decode(strip_tags($category_name), ENT_QUOTES)));
                    $worksheet->getStyleByColumnAndRow($column, $block_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_LEFT);
                    $this->wrap_text($worksheet, $column, $block_row);
                    
                    //                    dump('category row index: ' . $categrory_row_index);
                    //                    dump('cat row: ' . $block_row);
                    //                    
                    

                    if ($categrory_row_index == $category_count && $category_count != 1)
                    {
                        $worksheet->getStyleByColumnAndRow($column, $block_row)->getFont()->setBold(true);
                    }
                    
                    $row_index = 1;
                    //                    dump('row index: ' . $row_index);
                    

                    foreach ($block_content_data->get_rows() as $row_id => $row_name)
                    {
                        $column ++;
                        $worksheet->setCellValueByColumnAndRow($column, $block_row, $block_content_data->get_data_category_row($category_id, $row_id));
                        $worksheet->getStyleByColumnAndRow($column, $block_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment :: HORIZONTAL_CENTER);
                        //                        dump('row index: ' . $row_index);
                        //                        dump('row: ' . $block_row);
                        

                        if ($row_index == $row_count && $row_count != 1)
                        {
                            $worksheet->getStyleByColumnAndRow($column, $block_row)->getFont()->setBold(true);
                        }
                        
                        if ($categrory_row_index == $category_count && $category_count != 1)
                        {
                            $worksheet->getStyleByColumnAndRow($column, $block_row)->getFont()->setBold(true);
                        }
                        
                        $row_index ++;
                    }
                    $categrory_row_index ++;
                    $block_row ++;
                }
                
            //                exit();
            }
        
        }
    
    }

    private function wrap_text($worksheet, $colum, $row)
    {
        $worksheet->getStyleByColumnAndRow($colum, $row)->getAlignment()->setWrapText(true);
    
    }

    private function create_page_reporting_data($question)
    {
        $page_reporting_data = array();
        
        $all_participants_ids = $this->participants[self :: ALL_PARTICIPANTS];
        $reporting_data = $this->create_reporting_data($question, $all_participants_ids);
        
        $reporting_data_question = array();
        $reporting_data_question[self :: DATA_GROUP] = Translation :: get(AllGroups);
        $reporting_data_question[self :: DATA_NAME] = $question->get_title();
        $reporting_data_question[self :: DATA_DESCRIPTION] = $question->get_description();
        $reporting_data_question[self :: STARTED_PARTICIPANT_COUNT] = $this->participants[self :: STARTED_PARTICIPANT_COUNT];
        $reporting_data_question[self :: REPORTING_DATA] = $reporting_data;
        $page_reporting_data[] = $reporting_data_question;
        
        $groups = $this->participants[self :: GROUPS];
        foreach ($groups as $group)
        {
            $reporting_data_question = array();
            $reporting_data_question[self :: DATA_GROUP] = $group[self :: GROUP_DESCRIPTION];
            $reporting_data_question[self :: DATA_NAME] = $question->get_title();
            $reporting_data_question[self :: DATA_DESCRIPTION] = $question->get_description();
            $reporting_data_question[self :: STARTED_PARTICIPANT_COUNT] = $group[self :: STARTED_PARTICIPANT_COUNT];
            $all_participants_ids = $group[self :: ALL_PARTICIPANTS];
            $reporting_data = $this->create_reporting_data($question, $all_participants_ids);
            $reporting_data_question[self :: REPORTING_DATA] = $reporting_data;
            $page_reporting_data[] = $reporting_data_question;
        }
        
        return $page_reporting_data;
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
                    $reporting_data->add_category($option);
                    //                    dump('op key: '.$option_key);
                    //                    dump('option: '.$option);
                    foreach ($matches as $match_key => $match)
                    {
                        //                        dump('match key: '.$match_key);
                        //                    	dump('match: '.$match);
                        //                    	dump('answer_count: '.$answer_count[$option_key][$match_key]);
                        $totals[$match_key] = $totals[$match_key] + $answer_count[$option_key][$match_key];
                        //                        dump('total: '.$totals[$match_key]);
                        $reporting_data->add_data_category_row($option, strip_tags($match), $answer_count[$option_key][$match_key]);
                    }
                
                }
                
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
                    foreach ($matches as $match_key => $match)
                    {
                        $reporting_data->add_data_category_row(Translation :: get(self :: TOTAL), strip_tags($match), $totals[$match_key]);
                    }
                    //                    }
                

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
                if (count($options) > 1)
                {
                    $reporting_data->add_category(Translation :: get(self :: TOTAL));
                    foreach ($matches as $match)
                    {
                        $reporting_data->add_data_category_row(Translation :: get(self :: TOTAL), strip_tags($match), $total);
                    }
                }
                
                break;
            default :
                ;
                break;
        }
        
        return $reporting_data;
    }

    private function create_participants($ids)
    {
        
        $this->participants = array();
        $this->surveys = array();
        
        $surveys = array();
        foreach ($ids as $id)
        {
            $sv = array();
            $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
            $survey = $survey_publication->get_publication_object();
            $this->surveys[] = $survey;
            $survey_title = $survey->get_title();
            $survey_description = $survey->get_description();
            $sv[self :: SURVEY_NAME] = Utilities :: truncate_string(trim(strip_tags($survey_title)), 20, true, '');
            $sv[self :: SURVEY_DESCRIPTION] = Utilities :: truncate_string(trim(strip_tags($survey_description)), 20, true, '');
            $surveys[$id] = $sv;
        }
        
        $this->participants[self :: SURVEYS] = $surveys;
        $this->participants[self :: SURVEY_COUNT] = count($surveys);
        
        $condition = new InCondition(SurveyPublicationGroup :: PROPERTY_SURVEY_PUBLICATION, $ids);
        $publication_rel_groups = SurveyDataManager :: get_instance()->retrieve_survey_publication_groups($condition);
        
        $groups = array();
        $group_user_ids = array();
        $total_user_ids = array();
        while ($publication_rel_group = $publication_rel_groups->next_result())
        {
            $group = GroupDataManager :: get_instance()->retrieve_group($publication_rel_group->get_group_id());
            $groups[] = $group;
            $group_user_ids[$group->get_id()] = $group->get_users(true, true);
            $total_user_ids = array_merge($total_user_ids, $group_user_ids[$group->get_id()]);
        }
        
        $user_ids = array();
        
        $condition = new InCondition(SurveyPublicationUser :: PROPERTY_SURVEY_PUBLICATION, $ids);
        $publication_rel_users = SurveyDataManager :: get_instance()->retrieve_survey_publication_users($condition);
        
        while ($publication_rel_user = $publication_rel_users->next_result())
        {
            $user_ids[] = $publication_rel_user->get_user_id();
        }
        
        $total_user_ids = array_merge($total_user_ids, $user_ids);
        $total_user_ids = array_unique($total_user_ids);
        
        $conditions = array();
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $total_user_ids);
        $condition = new AndCondition($conditions);
        $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        
        $all_participants = array();
        $started_participants = array();
        $not_started_participants = array();
        
        $started_users = array();
        $not_started_users = array();
        
        while ($tracker = $trackers->next_result())
        {
            
            $all_participants[] = $tracker->get_id();
            
            switch ($tracker->get_status())
            {
                case SurveyParticipantTracker :: STATUS_NOTSTARTED :
                    $not_started_participants[] = $tracker->get_id();
                    $not_started_users[] = $tracker->get_user_id();
                    break;
                case SurveyParticipantTracker :: STATUS_STARTED :
                    $started_participants[] = $tracker->get_id();
                    $started_users[] = $tracker->get_user_id();
                    break;
                case SurveyParticipantTracker :: STATUS_FINISHED :
                    $started_participants[] = $tracker->get_id();
                    $started_users[] = $tracker->get_user_id();
                    break;
            }
        }
        
        $this->participants[self :: ALL_PARTICIPANTS] = $all_participants;
        $all_participant_count = count($all_participants);
        $this->participants[self :: ALL_PARTICIPANT_COUNT] = $all_participant_count;
        $this->participants[self :: NOT_STARTED_PARTICIPANTS] = $not_started_participants;
        $not_started_particpant_count = count($not_started_participants);
        $this->participants[self :: NOT_STARTED_PARTICIPANT_COUNT] = $not_started_particpant_count;
        $this->participants[self :: STARTED_PARTICIPANTS] = $started_participants;
        $started_participant_count = count($started_participants);
        $this->participants[self :: STARTED_PARTICIPANT_COUNT] = $started_participant_count;
        
        $participatie = $started_participant_count / $all_participant_count * 100;
        $participatie = number_format($participatie, 2);
        $this->participants[self :: PARTICIPATION_GRADE] = $participatie;
        
        foreach ($groups as $group)
        {
            
            $this->participants[self :: GROUPS][$group->get_id()][self :: GROUP_NAME] = $group->get_name();
            $this->participants[self :: GROUPS][$group->get_id()][self :: GROUP_DESCRIPTION] = $group->get_description();
            
            $group_users = $group_user_ids[$group->get_id()];
            
            $condition = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $group_users);
            $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
            
            $all_trackers = array();
            
            while ($tracker = $trackers->next_result())
            {
                $all_trackers[] = $tracker->get_id();
            }
            
            $all_tracker_count = count($all_trackers);
            $this->participants[self :: GROUPS][$group->get_id()][self :: ALL_PARTICIPANT_COUNT] = $all_tracker_count;
            $this->participants[self :: GROUPS][$group->get_id()][self :: ALL_PARTICIPANTS] = $all_trackers;
            
            $started = array_intersect($group_users, $started_users);
            
            $condition = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $started);
            $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
            
            $started_trackers = array();
            
            while ($tracker = $trackers->next_result())
            {
                $started_trackers[] = $tracker->get_id();
            }
            
            $started_tracker_count = count($started_trackers);
            $this->participants[self :: GROUPS][$group->get_id()][self :: STARTED_PARTICIPANT_COUNT] = $started_tracker_count;
            $this->participants[self :: GROUPS][$group->get_id()][self :: STARTED_PARTICIPANTS] = $started_trackers;
            
            $not_started = array_intersect($group_users, $not_started_users);
            
            $condition = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $not_started);
            $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
            
            $not_started_trackers = array();
            
            while ($tracker = $trackers->next_result())
            {
                $not_started_trackers[] = $tracker->get_id();
            }
            
            $this->participants[self :: GROUPS][$group->get_id()][self :: NOT_STARTED_PARTICIPANT_COUNT] = count($not_started_trackers);
            $this->participants[self :: GROUPS][$group->get_id()][self :: NOT_STARTED_PARTICIPANTS] = $not_started_trackers;
            
            $participatie = $started_tracker_count / $all_tracker_count * 100;
            $participatie = number_format($participatie, 2);
            $this->participants[self :: GROUPS][$group->get_id()][self :: PARTICIPATION_GRADE] = $participatie;
        
        }
    
    }
}

?>