<?php
require_once Path :: get_application_path() . 'lib/survey/survey_publication_group.class.php';
Path :: get_application_path() . 'lib/survey/survey_publication_user.class.php';
require_once (Path :: get_reporting_path() . 'lib/reporting_data.class.php');
require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class SurveyManagerSurveyExcelSyntaxExporterComponent extends SurveyManager
{
    
 	const STARTED_PARTICIPANTS = 'started_participants';
    const GROUP = 'group';
    const GROUPS = 'groups';
    const GROUP_NAME = 'group_name';
    const GROUP_DESCRIPTION = 'group_description';
    const VARIABLE_QUESTION_ID = 'question_id';
    const VARIABLE_NR = 'nr';
    const VARIABLE_NAME = 'Name';
    const VARIABLE_TYPE = 'Type';
    const VARIABLE_LABEL = 'Label';
    const VARIABLE_LEVEL_OF_MEASUREMENT = 'Measurement';
    const VARIABLE_MISSING_VALUE = 'Missing';
    const VARIABLE_ANSWER = 'answer';
    const VARIABLE_CODE = 'code';
    const VARIABLE_VALUES = 'Values';
    
    const SCALE_NOMINAL = 'nominal';
    const SCALE_ORDINAL = 'ordinal';
    const SCALE_RATIO = 'ratio';
    
    private $participants;
    private $surveys;
    private $questions;
    private $variable_encodings;
    private $variable_list;
    private $data_matrix;

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
        
        $worksheet = $excel->getSheet(0)->setTitle('Cases');
        
        $this->create_variable_encoding();
        
        $this->create_raw_data_set();
        
        $this->render_case_excel_data($worksheet);
        
        $worksheet = $excel->createSheet(1)->setTitle('Encoding');
        
        $this->render_encoding_excel($worksheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . 'survey_spss_export' . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory :: createWriter($excel, 'Excel2007');
        return $objWriter->save('php://output');
    }

    function create_variable_encoding($worksheet)
    {
        
        $this->variable_encodings = array();
        
        $this->variable_list = array();
        
        $variable_nr = 1;
        
        $variable_encoding = array();
        $variable_encoding[self :: VARIABLE_NAME] = self :: GROUP;
        $this->variable_list[self :: GROUP] = self :: GROUP;
        $variable_encoding[self :: VARIABLE_QUESTION_ID] = 0;
        $variable_encoding[self :: VARIABLE_LABEL] = 'Group';
        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = 99999;
        $variable_encoding[self :: VARIABLE_NR] = $variable_nr;
        $variable_nr ++;
        
        $groups = $this->participants[self :: GROUPS];
        $options = array();
        foreach ($groups as $id => $group)
        {
            $options[$id] = $group[self :: GROUP_DESCRIPTION];
        }
        
        $variable_encoding[self :: VARIABLE_VALUES] = $options;
        
        $this->variable_encodings[self :: GROUP] = $variable_encoding;
        
        $questions = $this->get_questions();
        $variable_nr = 1;
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
                    
                    foreach ($opts as $option_key => $option)
                    {
                        $variable_encoding = array();
                        
                        $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
                        $this->variable_list[$question_id][$option_key] = $variable_name . $var_index;
                        
                        $name = trim(html_entity_decode(strip_tags($option->get_value()), ENT_QUOTES));
                        
                        $variable_encoding[self :: VARIABLE_QUESTION_ID] = $question_id;
                        $variable_encoding[self :: VARIABLE_LABEL] = $name;
                        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
                        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
                        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = 99;
                        $variable_encoding[self :: VARIABLE_NR] = $variable_nr;
                        $variable_nr ++;
                        
                        $matchs = $question->get_matches();
                        $matches = array();
                        foreach ($matchs as $match)
                        {
                            $matches[] = trim(html_entity_decode(strip_tags($match), ENT_QUOTES));
                        }
                        
                        $variable_encoding[self :: VARIABLE_VALUES] = $matches;
                        $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
                        $var_index ++;
                    }
                    
                    break;
                case SurveyMultipleChoiceQuestion :: get_type_name() :
                    
                    $variable_encoding = array();
                    
                    $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
                    $this->variable_list[$question_id][0] = $variable_name . $var_index;
                    
                    $variable_encoding[self :: VARIABLE_QUESTION_ID] = $question_id;
                    $variable_encoding[self :: VARIABLE_LABEL] = trim(html_entity_decode(strip_tags($question->get_title()), ENT_QUOTES));
                    $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
                    $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
                    $variable_encoding[self :: VARIABLE_MISSING_VALUE] = 99;
                    $variable_encoding[self :: VARIABLE_NR] = $variable_nr;
                    $variable_nr ++;
                    
                    $opts = $question->get_options();
                    $options = array();
                    foreach ($opts as $option)
                    {
                        
                        $options[] = trim(html_entity_decode(strip_tags($option->get_value()), ENT_QUOTES));
                    }
                    
                    $variable_encoding[self :: VARIABLE_VALUES] = $options;
                    
                    $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
                    $var_index ++;
                    break;
            }
        
        }
       
    }

    private function create_raw_data_set()
    {
        
        $this->data_matrix = array();
        
        $vars = array_keys($this->variable_encodings);
        
        $participants = $this->participants[self :: STARTED_PARTICIPANTS];
        
        $questions = $this->get_questions();
        
        foreach ($participants as $participant_id)
        {
            $data = array();
            
            $data[$this->variable_list[self :: GROUP]] = $this->get_group_id($participant_id);
            
            foreach ($questions as $question)
            {
                
                $answer = null;
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
                    $answer = $tracker->get_answer();
                    $no_answer = false;
                
                }
                else
                {
                    $no_answer = true;
                }
                
                $type = $question->get_type();
                
                switch ($type)
                {
                    case SurveyMatrixQuestion :: get_type_name() :
                        
                        if ($no_answer)
                        {
                            
                            $options = $question->get_options();
                            foreach ($options as $key => $option)
                            {
                                $data[$this->variable_list[$question_id][$key]] = 99;
                            }
                        
                        }
                        else
                        {
                            foreach ($answer as $key => $option)
                            {
                                
                                foreach ($option as $match_key => $match)
                                {
                                    if ($question->get_matrix_type() == SurveyMatrixQuestion :: MATRIX_TYPE_CHECKBOX)
                                    {
                                        $data[$this->variable_list[$question_id][$key]] = $match_key;
                                    
                                    }
                                    else
                                    {
                                        $data[$this->variable_list[$question_id][$key]] = $match;
                                    
                                    }
                                }
                            }
                        }
                        
                        break;
                    case SurveyMultipleChoiceQuestion :: get_type_name() :
                        
                        if ($no_answer)
                        {
                            $data[$this->variable_list[$question_id][0]] = 99;
                        }
                        else
                        {
                            foreach ($answer as $key => $option)
                            {
                                if ($question->get_answer_type() == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
                                {
                                    $data[$this->variable_list[$question_id][0]] = $key;
                                }
                                else
                                {
                                    $data[$this->variable_list[$question_id][0]] = $option;
                                }
                            }
                        }
                        
                        break;
                }
            
            }
            
            $vars_set = array_keys($data);
            $vars_not_set = array_diff($vars, $vars_set);
            foreach ($vars_not_set as $var)
            {
                $data[$var] = 99;
            }
            
            $this->data_matrix[$participant_id] = $data;
        }
    }

    private function get_group_id($participant_id)
    {
        
        $groups = $this->participants[self :: GROUPS];
        
        foreach ($groups as $id => $group)
        {
            $participants = $group[self :: STARTED_PARTICIPANTS];
            if (in_array($participant_id, $participants))
            {
                return $id;
            }
        }
        
        return 99999;
    }

    private function get_questions()
    {
        
        if (isset($this->questions))
        {
            return $this->questions;
        }
        
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
        
        $this->questions = $page_questions;
        
        return $this->questions;
    
    }

    private function render_case_excel_data($worksheet)
    {
        $column = 0;
        
        foreach ($this->variable_encodings as $name => $encoding)
        {
            $row = 1;
            $worksheet->setCellValueByColumnAndRow($column, $row, $name);
            
            $row ++;
            
            foreach ($this->data_matrix as $paticipant_id => $data)
            {
                $worksheet->setCellValueByColumnAndRow($column, $row, $data[$name]);
                $row ++;
            }
            
            $column ++;
        }
    
    }

    private function render_encoding_excel($worksheet)
    {
        
        $headers = array(self :: VARIABLE_NAME, self :: VARIABLE_TYPE, self :: VARIABLE_LABEL, self :: VARIABLE_VALUES, self :: VARIABLE_MISSING_VALUE, self :: VARIABLE_LEVEL_OF_MEASUREMENT);
        
        $encodings = $this->variable_encodings;
        $var_count = count($encodings);
        $column = 0;
        $row = 1;
        $worksheet->getColumnDimensionByColumn(2)->setWidth(50);
        $worksheet->getColumnDimensionByColumn(3)->setAutoSize(true);
        $worksheet->getColumnDimensionByColumn(5)->setWidth(20);
        
        foreach ($headers as $header)
        {
            
            if ($header == self :: VARIABLE_VALUES)
            {
                $worksheet->setCellValueByColumnAndRow($column, $row, 'Values');
            }
            else
            {
                $worksheet->setCellValueByColumnAndRow($column, $row, $header);
            }
            
            foreach ($encodings as $encoding)
            {
                $row ++;
                if ($header == self :: VARIABLE_VALUES)
                {
                    $values = $encoding[$header];
                    $encoded_values = array();
                    foreach ($values as $key => $val)
                    {
                        $encoded_values[] = $key . '=' . $val;
                    }
                    $values = implode("\n", $encoded_values);
                    $worksheet->setCellValueByColumnAndRow($column, $row, $values);
                    $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setWrapText(true);
                    $this->wrap_text($worksheet, $column, $row);
                }
                else
                {
                    $worksheet->setCellValueByColumnAndRow($column, $row, $encoding[$header]);
                    $worksheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment :: VERTICAL_TOP);
                    if ($header == self :: VARIABLE_LABEL)
                    {
                        $this->wrap_text($worksheet, $column, $row);
                    }
                }
            
            }
            $row = $row - $var_count;
            $column ++;
        }
    
    }

    private function wrap_text($worksheet, $colum, $row)
    {
        $worksheet->getStyleByColumnAndRow($colum, $row)->getAlignment()->setWrapText(true);
    
    }

    private function create_participants($ids)
    {
        
        $this->participants = array();
        $this->surveys = array();
        
        foreach ($ids as $id)
        {
            $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
            $survey = $survey_publication->get_publication_object();
            $this->surveys[] = $survey;
        }
        
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
        
        $started_status = array(SurveyParticipantTracker :: STATUS_STARTED, SurveyParticipantTracker :: STATUS_FINISHED);
        
        $conditions = array();
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_STATUS, $started_status);
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $total_user_ids);
        $condition = new AndCondition($conditions);
        $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        
        $started_participants = array();
        $started_users = array();
        
        while ($tracker = $trackers->next_result())
        {
            $started_participants[] = $tracker->get_id();
            $started_users[] = $tracker->get_user_id();
        }
        
        $this->participants[self :: STARTED_PARTICIPANTS] = $started_participants;
        
        foreach ($groups as $group)
        {
            
            $this->participants[self :: GROUPS][$group->get_id()][self :: GROUP_NAME] = $group->get_name();
            $this->participants[self :: GROUPS][$group->get_id()][self :: GROUP_DESCRIPTION] = $group->get_description();
            
            $group_users = $group_user_ids[$group->get_id()];
            
            $started = array_intersect($group_users, $started_users);
            
            $condition = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $started);
            $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
            
            $started_trackers = array();
            
            while ($tracker = $trackers->next_result())
            {
                $started_trackers[] = $tracker->get_id();
            }
            
            $this->participants[self :: GROUPS][$group->get_id()][self :: STARTED_PARTICIPANTS] = $started_trackers;
        
        }
    }
}

?>