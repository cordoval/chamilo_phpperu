<?php
require_once Path :: get_application_path() . 'lib/survey/survey_publication_group.class.php';
Path :: get_application_path() . 'lib/survey/survey_publication_user.class.php';
require_once (Path :: get_reporting_path() . 'lib/reporting_data.class.php');
require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class SurveyManagerSurveySpssSyntaxExporterComponent extends SurveyManager
{
    const SYNTAX_DATA_LIST = 'DATA LIST ';
    const SYNTAX_BEGIN_DATA = 'BEGIN DATA';
    const SYNTAX_END_DATA = 'END DATA.';
    const SYNTAX_RECORDS = 'RECORDS=';
    const SYNTAX_VALUE_LABELS = 'VALUE LABELS';
    const SYNTAX_VARIABLE_LABELS = 'VARIABLE LABELS';
    const SYNTAX_VARIABLE_LEVEL = 'VARIABLE LEVEL';
    const SYNTAX_MISSING_VALUES = 'MISSING VALUES';
    
    const STARTED_PARTICIPANTS = 'started_participants';
    const GROUP = 'group';
    const GROUPS = 'groups';
    const GROUP_NAME = 'group_name';
    const GROUP_DESCRIPTION = 'group_description';
    
    const CASE_USER_ID = 'user';
    const CASE_PARTICIPANT_ID = 'participant_id';
    const CASE_TEMPLATE_ID = 'template_id';
    const CASE_PARTICIPANT = 'participant';
    const CASE_PARTICIPANTS = 'participants';
    
    const OPLEIDING = 'Opleiding';
    const OPLEIDINGS_ONDERDEEL = 'Opleidingsonderdeel';
    const CONTEXT = 'context';
    const CONTEXT_TYPE = 'context_type';
    const CONTEXT_TYPE_NAME_VALUE = 'context_type_name_value';
    const CONTEXT_TYPE_NAME_ID = 'context_type_name_id';
    
    const VARIABLE_QUESTION_ID = 'question_id';
    const VARIABLE_NR = 'nr';
    const VARIABLE_NAME = 'Name';
    const VARIABLE_TYPE = 'Type';
    const VARIABLE_TYPE_FORMAT = 'type_format';
    const VARIABLE_LABEL = 'Label';
    const VARIABLE_LEVEL_OF_MEASUREMENT = 'Measurement';
    const VARIABLE_MISSING_VALUE = 'Missing';
    const VARIABLE_ANSWER = 'answer';
    const VARIABLE_CODE = 'code';
    const VARIABLE_VALUES = 'Values';
    
    const SCALE_NOMINAL = '(NOMINAL)';
    const SCALE_ORDINAL = '(ORDINAL)';
    const SCALE_SCALE = '(SCALE)';
    const MISSING_VALUE = '999999';
    
    private $participants;
    private $cases;
    //    private $trackers;
    private $surveys;
    private $questions;
    private $all_questions;
    
    private $variable_encodings;
    private $variable_list;
    private $data_matrix;
    private $contexts;
    private $context_types;

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
        $this->create_variable_encoding();
        //        $this->create_raw_data_set();
        

        $this->render_data();
    
    }

    public function render_data()
    {
        
        $filename = 'spss_syntax.sps';
        $this->get_content($filename);
        $temp_directory = Path :: get_temp_path();
        $path = $temp_directory . $filename;
        Filesystem :: file_send_for_download($path, true, $filename);
        Filesystem :: remove($path);
    

    //    	$content = $this->get_content();
    //        $filename = 'spss_syntax.sps';
    //        $temp_directory = Path :: get_temp_path();
    //        $path = $temp_directory . $filename;
    //        Filesystem :: write_to_file($path, $content);
    //        Filesystem :: file_send_for_download($path, true, $filename);
    //        Filesystem :: remove($path);
    }

    private function create_variable_encoding()
    {
        
        //test
        $nominal_question_ids = array(- 1, - 2, - 3, 264, 263, 262);
        
        $this->variable_encodings = array();
        
        $this->variable_list = array();
        
        $variable_name = 'Var';
        $var_index = 1;
        
        $variable_encoding = array();
        $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
        $this->variable_list[self :: CASE_USER_ID] = $variable_name . $var_index;
        
        $variable_encoding[self :: VARIABLE_QUESTION_ID] = - 1;
        $variable_encoding[self :: VARIABLE_LABEL] = CASE_USER_ID;
        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
        $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
        $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
        $variable_encoding[self :: VARIABLE_NR] = $var_index;
        
        $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
        $var_index ++;
        
        //        $variable_encoding = array();
        //        $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
        //        $this->variable_list[self :: GROUP] = $variable_name . $var_index;
        //        
        //        $variable_encoding[self :: VARIABLE_QUESTION_ID] = 0;
        //        $variable_encoding[self :: VARIABLE_LABEL] = self :: GROUP;
        //        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
        //        $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
        //        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
        //        $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
        //        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
        //        $variable_encoding[self :: VARIABLE_NR] = $var_index;
        //        
        //        $groups = $this->participants[self :: GROUPS];
        //        $values = array();
        //        foreach ($groups as $id => $group)
        //        {
        //            $values[$id] = $group[self :: GROUP_DESCRIPTION];
        //        }
        //        
        //
        //        $variable_encoding[self :: VARIABLE_VALUES] = $values;
        //        
        //        $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
        //        $var_index ++;
        

        $variable_encoding = array();
        $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
        $this->variable_list[self :: CONTEXT] = $variable_name . $var_index;
        $variable_encoding[self :: VARIABLE_QUESTION_ID] = - 2;
        $variable_encoding[self :: VARIABLE_LABEL] = self :: CONTEXT;
        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
        $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
        $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
        $variable_encoding[self :: VARIABLE_NR] = $var_index;
        
        $contexts = $this->contexts;
        $values = array();
        foreach ($contexts as $id => $context)
        {
            $values[$id] = $context;
        }
        $variable_encoding[self :: VARIABLE_VALUES] = $values;
        
        $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
        $var_index ++;
        
        $variable_encoding = array();
        $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
        $this->variable_list[self :: CONTEXT_TYPE] = $variable_name . $var_index;
        $variable_encoding[self :: VARIABLE_QUESTION_ID] = - 3;
        $variable_encoding[self :: VARIABLE_LABEL] = self :: CONTEXT_TYPE;
        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
        $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
        $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
        $variable_encoding[self :: VARIABLE_NR] = $var_index;
        
        $context_types = $this->context_types;
        $values = array();
        foreach ($context_types as $id => $context_type)
        {
            $values[$context_type[self :: CONTEXT_TYPE_NAME_ID]] = $context_type[self :: CONTEXT_TYPE_NAME_VALUE];
        }
        
        $variable_encoding[self :: VARIABLE_VALUES] = $values;
        $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
        $var_index ++;
        
        //the question variables
        $questions = $this->get_all_questions();
        
        //        dump($questions);
        //        exit;
        

        foreach ($questions as $question_id => $question)
        {
            
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
                        $name = preg_replace("/[\n\r]/", "", $name);
                        $variable_encoding[self :: VARIABLE_QUESTION_ID] = $question_id;
                        $variable_encoding[self :: VARIABLE_LABEL] = $name;
                        if (in_array($question_id, $nominal_question_ids))
                        {
                            $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
                            $this->variable_list[self :: SCALE_NOMINAL][$question_id . '-' . $option_key] = $variable_encoding[self :: VARIABLE_NAME];
                        }
                        else
                        {
                            $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_ORDINAL;
                            $this->variable_list[self :: SCALE_ORDINAL][$question_id . '-' . $option_key] = $variable_encoding[self :: VARIABLE_NAME];
                        }
                        
                        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
                        $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
                        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
                        $variable_encoding[self :: VARIABLE_NR] = $var_index;
                        
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
                    $name = trim(html_entity_decode(strip_tags($question->get_title()), ENT_QUOTES));
                    $name = preg_replace("/[\n\r]/", "", $name);
                    
                    $variable_encoding[self :: VARIABLE_LABEL] = $name;
                    if (in_array($question_id, $nominal_question_ids))
                    {
                        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
                        $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
                    }
                    else
                    {
                        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_ORDINAL;
                        $this->variable_list[self :: SCALE_ORDINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
                    }
                    $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
                    $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
                    $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
                    $variable_encoding[self :: VARIABLE_NR] = $var_index;
                    
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
        //                            dump($this->variable_encodings);
    //             dump(array_keys($this->variable_encodings));
    //                     exit;
    //            dump($this->variable_list);
    //            exit;
    }

    private function get_case_data($case)
    {
        //                    dump($case);
        

        $vars = array_keys($this->variable_encodings);
        
        //        $participants = $this->participants[self :: STARTED_PARTICIPANTS];
        

        $questions = $this->get_questions();
        
        $data = array();
        $data[$this->variable_list[self :: CASE_USER_ID]] = $case[self :: CASE_USER_ID];
        
        //            dump($data);
        //            exit;
        

        foreach ($case[self :: CASE_PARTICIPANTS] as $participant)
        {
            
            //            dump($participant);
            //            $data[$this->variable_list[self :: GROUP]] = $this->get_group_id($participant_id);
            $tracker = $participant[self :: CASE_PARTICIPANT];
            $data[$this->variable_list[self :: CONTEXT]] = $tracker->get_context_id();
            $template_id = $tracker->get_context_template_id();
            $data[$this->variable_list[self :: CONTEXT_TYPE]] = $template_id;
            $participant_id = $participant[self :: CASE_PARTICIPANT_ID];
            
            foreach ($questions[$template_id] as $question)
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
                                $data[$this->variable_list[$question_id][$key]] = self :: MISSING_VALUE;
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
                            $data[$this->variable_list[$question_id][0]] = self :: MISSING_VALUE;
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
        
        }
        $vars_set = array_keys($data);
        $vars_not_set = array_diff($vars, $vars_set);
        
        foreach ($vars_not_set as $var)
        {
            $data[$var] = self :: MISSING_VALUE;
        }
        
        //        dump($data);
        

        return $data;
    }

    //    private function create_raw_data_set()
    //    {
    //        
    //        $this->data_matrix = array();
    //        
    //        $cases = $this->cases;
    //
    //        foreach ($cases as $case)
    //        {
    //            $this->data_matrix[$participant_id] = $this->get_case_data($case);
    //        }
    //        
    //            dump($this->data_matrix);
    //            exit();
    //    }
    

    private function get_content($filename)
    {
        
        $temp_directory = Path :: get_temp_path();
        $path = $temp_directory . $filename;
        
        $content = array();
        $var_count = count($this->variable_encodings);
        $records_per_case = number_format($var_count / 10, 0);
        
        // add de variable definition and nr of records that make together one case
        $content[] = self :: SYNTAX_DATA_LIST . self :: SYNTAX_RECORDS . $records_per_case;
        
        $index = 0;
        $records_count = $records_per_case;
        while ($records_count > 0)
        {
            $slice = array_slice($this->variable_encodings, $index, 10);
            $var_content = array();
            foreach ($slice as $var => $encoding)
            {
                
                $var_content[] = $var . ' ' . $encoding[self :: VARIABLE_TYPE_FORMAT];
            }
            
            $content[] = '/ ' . implode(' ', $var_content);
            $records_count --;
            $index = $index + 10;
        }
        
        $content[] = '.';
        
        //add the inline data
        $content[] = self :: SYNTAX_BEGIN_DATA;
        $content[] = '';
        
        $content = implode("\n", $content);
        
        Filesystem :: write_to_file($path, $content, true);
        
        //        dump('hi');
        //        exit;
        

        $cases = $this->cases;
        
        foreach ($cases as $case)
        {
            $content = array();
            $data = $this->get_case_data($case);
            $index = 0;
            $records_count = $records_per_case;
            
            while ($records_count > 0)
            {
                $slice = array_slice($this->variable_encodings, $index, 10);
                //           	dump($slice);
                $data_content = array();
                foreach ($slice as $var => $encoding)
                {
                    
                    $value = $data[$var];
                    $columns = strlen($value);
                    switch ($columns)
                    {
                        case 5 :
                            $value = '0' . $value;
                            break;
                        case 4 :
                            $value = '00' . $value;
                            ;
                            break;
                        case 3 :
                            $value = '000' . $value;
                            break;
                        case 2 :
                            $value = '0000' . $value;
                            break;
                        case 1 :
                            $value = '00000' . $value;
                            break;
                    }
                    $data_content[] = $value;
                }
                $content[] = implode('', $data_content);
                $records_count --;
                $index = $index + 10;
            }
            
            $content = implode("\n", $content);
            Filesystem :: write_to_file($path, $content, true);
        }
        
        //        foreach ($this->data_matrix as $data)
        //        {
        //            $index = 0;
        //            $records_count = $records_per_case;
        //            while ($records_count > 0)
        //                while ($records_count > 0)
        //                {
        //                    $slice = array_slice($this->variable_encodings, $index, 10);
        //                    //           	dump($slice);
        //                    $data_content = array();
        //                    foreach ($slice as $var => $encoding)
        //                    {
        //                        
        //                        $value = $data[$var];
        //                        $columns = strlen($value);
        //                        switch ($columns)
        //                        {
        //                            case 5 :
        //                                $value = '0' . $value;
        //                                break;
        //                            case 4 :
        //                                $value = '00' . $value;
        //                                ;
        //                                break;
        //                            case 3 :
        //                                $value = '000' . $value;
        //                                break;
        //                            case 2 :
        //                                $value = '0000' . $value;
        //                                break;
        //                            case 1 :
        //                                $value = '00000' . $value;
        //                                break;
        //                        }
        //                        $data_content[] = $value;
        //                    }
        //                    $content[] = implode('', $data_content);
        //                    $records_count --;
        //                    $index = $index + 10;
        //                }
        //        }
        

        $content = array();
        $content[] = '';
        $content[] = self :: SYNTAX_END_DATA;
        
        //add de value labels
        $content[] = self :: SYNTAX_VALUE_LABELS;
        $content[] = $this->create_value_labels() . ' .';
        
        //add measure levels
        $content[] = self :: SYNTAX_VARIABLE_LEVEL;
        $content[] = $this->create_measure_levels() . ' .';
        
        //add variable labels
        $content[] = self :: SYNTAX_VARIABLE_LABELS;
        $content[] = $this->create_variable_labels() . ' .';
        
        //add missing values
        $content[] = self :: SYNTAX_MISSING_VALUES;
        $content[] = $this->create_missing_values() . ' .';
        
        $content = implode("\n", $content);
        //        dump($content);
        //        exit();
        

        Filesystem :: write_to_file($path, $content, true);
        
    //        return $content;
    }

    private function create_value_labels()
    {
        $value_labels = array();
        $index = 0;
        foreach ($this->variable_encodings as $var => $encoding)
        {
            if ($index == 0)
            {
                $label = $var . ' ';
            }
            else
            {
                $label = '/' . $var . ' ';
            }
            $value_label = array();
            foreach ($encoding[self :: VARIABLE_VALUES] as $key => $value)
            {
                $value_label[] = $key . ' \'' . $value . '\'';
            }
            $labels = implode("\n", $value_label);
            $label = $label . $labels;
            $value_labels[] = $label;
            $index ++;
        }
        
        return implode("\n", $value_labels);
    
    }

    private function create_measure_levels()
    {
        $measure_levels = array();
        $index = 0;
        foreach ($this->variable_encodings as $var => $encoding)
        {
            if ($index == 0)
            {
                $label = $var . ' ';
            }
            else
            {
                $label = '/' . $var . ' ';
            }
            $measure_level = $label . ' ' . $encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT];
            $measure_levels[] = $measure_level;
            $index ++;
        }
        
        return implode("\n", $measure_levels);
    
    }

    private function create_variable_labels()
    {
        $variable_labels = array();
        $index = 0;
        foreach ($this->variable_encodings as $var => $encoding)
        {
            if ($index == 0)
            {
                $label = $var . ' ';
            }
            else
            {
                $label = '/' . $var . ' ';
            }
            $variable_label = $label . ' \'' . $encoding[self :: VARIABLE_LABEL] . '\'';
            $variable_labels[] = $variable_label;
            $index ++;
        }
        
        return implode("\n", $variable_labels);
    
    }

    private function create_missing_values()
    {
        $missing_values = array();
        $index = 0;
        foreach ($this->variable_encodings as $var => $encoding)
        {
            if ($index == 0)
            {
                $label = $var . ' ';
            }
            else
            {
                $label = '/' . $var . ' ';
            }
            $missing_value = $label . ' (' . $encoding[self :: VARIABLE_MISSING_VALUE] . ')';
            $missing_values[] = $missing_value;
            $index ++;
        }
        
        return implode("\n", $missing_values);
    
    }

    //    private function get_group_id($participant_id)
    //    {
    //        
    //        $groups = $this->participants[self :: GROUPS];
    //        
    //        foreach ($groups as $id => $group)
    //        {
    //            $participants = $group[self :: STARTED_PARTICIPANTS];
    //            if (in_array($participant_id, $participants))
    //            {
    //                return $id;
    //            }
    //        }
    //        
    //        return self :: MISSING_VALUE;
    //    }
    

    private function get_questions($template_id)
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
                $conditions = array();
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $survey->get_id());
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $page->get_id());
                $condition = new AndCondition($conditions);
                $survey_rel_page = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition)->next_result();
                $template_id = $survey_rel_page->get_template_id();
                
                if ($page->count_questions() != 0)
                {
                    $questions = $page->get_questions();
                    
                    foreach ($questions as $question)
                    {
                        $type = $question->get_type();
                        if ($type == SurveyMultipleChoiceQuestion :: get_type_name() || $type == SurveyMatrixQuestion :: get_type_name())
                        {
                            
                            $page_questions[$template_id][$question->get_id()] = $question;
                        }
                    
                    }
                }
            }
        }
        
        $this->questions = $page_questions;
        
        return $this->questions;
    
    }

    private function get_all_questions()
    {
        
        if (isset($this->all_questions))
        {
            return $this->all_questions;
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
                        $type = $question->get_type();
                        if ($type == SurveyMultipleChoiceQuestion :: get_type_name() || $type == SurveyMatrixQuestion :: get_type_name())
                        {
                            
                            $page_questions[$question->get_id()] = $question;
                        }
                    }
                }
            }
        }
        
        $this->all_questions = $page_questions;
        
        return $this->all_questions;
    
    }

    private function create_participants($ids)
    {
        
        $this->participants = array();
        $this->surveys = array();
        
        $context_templates = array();
        
        foreach ($ids as $id)
        {
            $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
            $survey = $survey_publication->get_publication_object();
            $this->surveys[] = $survey;
            $context_templates[$survey->get_context_template_id()] = $survey->get_context_template();
        }
        //        
        //        dump($context_templates);
        

        //		dump($this->get_questions());
        //        exit;
        $this->context_types = array();
        
        $parent_child_context_ids = array();
        
        $index = - 1;
        foreach ($context_templates as $template)
        {
            $index ++;
            $context_type = array();
            $context_type[self :: CONTEXT_TYPE_NAME_VALUE] = $template->get_name();
            $context_type[self :: CONTEXT_TYPE_NAME_ID] = $index;
            $this->context_types[$template->get_id()] = $context_type;
            
            $children = $template->get_children(false);
            while ($child = $children->next_result())
            {
                $index ++;
                $context_type = array();
                $context_type[self :: CONTEXT_TYPE_NAME_VALUE] = $child->get_name();
                $context_type[self :: CONTEXT_TYPE_NAME_ID] = $index;
                $this->context_types[$child->get_id()] = $context_type;
                $parent_child_context_ids[$template->get_id()] = $child->get_id();
            }
        
        }
        //                dump($parent_child_context_ids);
        //                        exit();
        

        //        $condition = new InCondition(SurveyPublicationGroup :: PROPERTY_SURVEY_PUBLICATION, $ids);
        //        $publication_rel_groups = SurveyDataManager :: get_instance()->retrieve_survey_publication_groups($condition);
        //        
        //        $groups = array();
        //        $group_user_ids = array();
        //        $total_user_ids = array();
        //        while ($publication_rel_group = $publication_rel_groups->next_result())
        //        {
        //            $group = GroupDataManager :: get_instance()->retrieve_group($publication_rel_group->get_group_id());
        //            $groups[] = $group;
        //            $group_user_ids[$group->get_id()] = $group->get_users(true, true);
        //            $total_user_ids = array_merge($total_user_ids, $group_user_ids[$group->get_id()]);
        //        }
        //        
        //        $user_ids = array();
        //        
        //        $condition = new InCondition(SurveyPublicationUser :: PROPERTY_SURVEY_PUBLICATION, $ids);
        //        $publication_rel_users = SurveyDataManager :: get_instance()->retrieve_survey_publication_users($condition);
        //        
        //        while ($publication_rel_user = $publication_rel_users->next_result())
        //        {
        //            $user_ids[] = $publication_rel_user->get_user_id();
        //        }
        //        
        //        $total_user_ids = array_merge($total_user_ids, $user_ids);
        //        $total_user_ids = array_unique($total_user_ids);
        //        
        //        dump(count($total_user_ids));
        

        //        
        //        $conditions = array();
        //        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_STATUS, $started_status);
        //        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);
        //        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $total_user_ids);
        //        $condition = new AndCondition($conditions);
        //        
        //        $total_tracker_count = Tracker :: count_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        //        
        //        dump($total_tracker_count);
        //        dump($condition);
        //              exit;
        

        $started_status = array(SurveyParticipantTracker :: STATUS_STARTED, SurveyParticipantTracker :: STATUS_FINISHED);
        
        $conditions = array();
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_STATUS, $started_status);
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID, $parent_child_context_ids);
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);
        //        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $total_user_ids);
        //        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_PARENT_ID, 0);
        $condition = new AndCondition($conditions);
        
        $tracker_count = Tracker :: count_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        
        //                dump($tracker_count);
        

        //		var_dump($this->get_questions());
        

        //		exit;
        

        $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        
        //        $started_participants = array();
        //        $started_users = array();
        $this->contexts = array();
        $this->cases = array();
        $case_id = 1;
        
        while ($tracker = $trackers->next_result())
        {
            $case = array();
            $case[self :: CASE_USER_ID] = $tracker->get_user_id();
            
            $participants = array();
            
            $participant = array();
            
            $participant[self :: CASE_TEMPLATE_ID] = $tracker->get_context_template_id();
            $participant[self :: CASE_PARTICIPANT_ID] = $tracker->get_id();
            $participant[self :: CASE_PARTICIPANT] = $tracker;
            $participants[] = $participant;
            //            $conditions = array();
            //        	$conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $tracker->get_user_id());
            //        	$conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $ids);
            //        	$conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $total_user_ids);
            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $tracker->get_parent_id());
            //        	$condition = new AndCondition($conditions);
            

            $parent_tracker = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition)->next_result();
            
            $participant = array();
            $participant[self :: CASE_TEMPLATE_ID] = $parent_tracker->get_context_template_id();
            $participant[self :: CASE_PARTICIPANT_ID] = $parent_tracker->get_id();
            $participant[self :: CASE_PARTICIPANT] = $parent_tracker;
            $participants[] = $participant;
            
            //        	while ($sub_tracker = $tracker->get_id()->next_result()){
            //            	
            //            }
            

            $case[self :: CASE_PARTICIPANTS] = $participants;
            $this->cases[$case_id] = $case;
            $case_id ++;
            //        	$started_participants[] = $tracker->get_id();
            //            $started_users[] = $tracker->get_user_id();
            $this->contexts[$tracker->get_context_id()] = $tracker->get_context_name();
            //            $this->trackers[$tracker->get_id()] = $tracker;
            

//            break;
        }
        
    //        $this->participants[self :: STARTED_PARTICIPANTS] = $started_participants;
    

    //            dump($this->cases);
    //            exit();
    

    //        foreach ($groups as $group)
    //        {
    //            
    //            $this->participants[self :: GROUPS][$group->get_id()][self :: GROUP_NAME] = $group->get_name();
    //            $this->participants[self :: GROUPS][$group->get_id()][self :: GROUP_DESCRIPTION] = $group->get_description();
    //            
    //            $group_users = $group_user_ids[$group->get_id()];
    //            
    //            $started = array_intersect($group_users, $started_users);
    //            
    //            if (count($started) == 0)
    //            {
    //                $started = array(0);
    //            }
    //            
    //            $condition = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $started);
    //            
    //            $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
    //            
    //            $started_trackers = array();
    //            
    //            while ($tracker = $trackers->next_result())
    //            {
    //                $started_trackers[] = $tracker->get_id();
    //            }
    //            
    //            if (count($started_trackers) == 0)
    //            {
    //                $started_participants = array(0);
    //            }
    //            
    //            $this->participants[self :: GROUPS][$group->get_id()][self :: STARTED_PARTICIPANTS] = $started_trackers;
    //        
    //        }
    }
}

?>