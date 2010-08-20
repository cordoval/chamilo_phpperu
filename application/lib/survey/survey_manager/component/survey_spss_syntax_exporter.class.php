<?php
require_once (Path :: get_application_path() . 'lib/survey/survey_publication_group.class.php');
require_once (Path :: get_application_path() . 'lib/survey/survey_publication_user.class.php');
require_once (Path :: get_reporting_path() . 'lib/reporting_data.class.php');
require_once (Path :: get_plugin_path() . 'phpexcel/PHPExcel.php');

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
    const CASE_TEMPLATE_LEVEL = 'template_level';
    const CASE_TEMPLATE_ID = 'template_id';
    const CASE_CONTEXT_ID = 'context_id';
    const CASE_PARTICIPANT = 'participant';
    const CASE_PARTICIPANTS = 'participants';
    
    const CONTEXT = 'context Level:';
    const CONTEXT_NAME_VALUES = 'context_name_values';
    const CONTEXT_NAME_VALUE = 'context_name_value';
    const CONTEXT_NAME_ID = 'context_name_id';
    
    const CONTEXT_TYPE = 'context type Level:';
    const CONTEXT_TYPE_NAME_VALUES = 'context_type_name_values';
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
    
    private $cases;
    //    private $trackers;
    private $surveys;
    private $publication_ids;
    private $questions;
    private $all_questions;
    
    private $variable_encodings;
    private $variable_list;
    private $template_levels;
    
    private $answer_matrix;

    //    private $participant_ids;
    //    private $context_variables;
    //    private $contexts;
    //    private $context_types;
    

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
        
        $this->set_publication_ids($ids);
//        
//        $this->get_cases();
//        exit;
        
        //        $this->create_cases($ids);
        $this->create_variable_encoding();
        
        $this->render_data();
    
    }

    public function render_data()
    {
        
        $filename = 'spss_syntax.sps';
        $temp_directory = Path :: get_temp_path();
        $path = $temp_directory . $filename;
        
        $this->get_content($path);
        
        Filesystem :: file_send_for_download($path, true, $filename);
        Filesystem :: remove($path);
    
    }

    private function create_variable_encoding($ids)
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
        $variable_encoding[self :: VARIABLE_LABEL] = self :: CASE_USER_ID;
        $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
        $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
        $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
        $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
        $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
        $variable_encoding[self :: VARIABLE_NR] = $var_index;
        
        $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
        $var_index ++;
        
        //for each level in the template structure we need a context en context_type variable
        

        $context_variables = $this->get_context_variables($this->get_publication_ids());
        //        dump($this->context_variables);
        //        exit();
        foreach ($context_variables as $level => $context_variable)
        {
            $variable_encoding = array();
            $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
            $this->variable_list[self :: CONTEXT . $level] = $variable_name . $var_index;
            $variable_encoding[self :: VARIABLE_QUESTION_ID] = - 2;
            $variable_encoding[self :: VARIABLE_LABEL] = self :: CONTEXT . $level;
            $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
            $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
            $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
            $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
            $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
            $variable_encoding[self :: VARIABLE_NR] = $var_index;
            
            $contexts = $context_variable[self :: CONTEXT_NAME_VALUES];
            $values = array();
            foreach ($contexts as $id => $context)
            {
                $values[$context[self :: CONTEXT_NAME_ID]] = $context[self :: CONTEXT_NAME_VALUE];
            }
            $variable_encoding[self :: VARIABLE_VALUES] = $values;
            
            $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
            $var_index ++;
            
            $variable_encoding = array();
            $variable_encoding[self :: VARIABLE_NAME] = $variable_name . $var_index;
            $this->variable_list[self :: CONTEXT_TYPE . $level] = $variable_name . $var_index;
            $variable_encoding[self :: VARIABLE_QUESTION_ID] = - 3;
            $variable_encoding[self :: VARIABLE_LABEL] = self :: CONTEXT_TYPE . $level;
            ;
            $variable_encoding[self :: VARIABLE_LEVEL_OF_MEASUREMENT] = self :: SCALE_NOMINAL;
            $this->variable_list[self :: SCALE_NOMINAL][$variable_encoding[self :: VARIABLE_QUESTION_ID]] = $variable_encoding[self :: VARIABLE_NAME];
            $variable_encoding[self :: VARIABLE_TYPE] = 'numeric';
            $variable_encoding[self :: VARIABLE_TYPE_FORMAT] = '(F6.0)';
            $variable_encoding[self :: VARIABLE_MISSING_VALUE] = self :: MISSING_VALUE;
            $variable_encoding[self :: VARIABLE_NR] = $var_index;
            
            $context_types = $context_variable[self :: CONTEXT_TYPE_NAME_VALUES];
            $values = array();
            foreach ($context_types as $context_type)
            {
                $values[$context_type[self :: CONTEXT_TYPE_NAME_ID]] = $context_type[self :: CONTEXT_TYPE_NAME_VALUE];
            }
            
            $variable_encoding[self :: VARIABLE_VALUES] = $values;
            $this->variable_encodings[$variable_name . $var_index] = $variable_encoding;
            $var_index ++;
        }
        
        //the question variables
        $all_questions = $this->get_all_questions();
        
        //       dump($all_questions);
        //        exit();
        

        foreach ($all_questions as $question_id => $question)
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
        
    //                                    dump($this->variable_encodings);
    //                                    exit;
    //             dump(array_keys($this->variable_encodings));
    //                     exit;
    //        dump($this->variable_list);
    //        exit();
    }

    private function get_case_data($case)
    {
        
        $temp_directory = Path :: get_temp_path();
        
        $questions = $this->get_questions();
        
        $data = array();
        $data[$this->variable_list[self :: CASE_USER_ID]] = $case[self :: CASE_USER_ID];
        
        foreach ($case[self :: CASE_PARTICIPANTS] as $participant_id => $participant)
        {
            
            $participant_data = array();
            
            $template_id = $participant[self :: CASE_TEMPLATE_ID];
            
//            $file_name = md5($participant_id . $template_id);
//            $path = $temp_directory . $file_name;
//            
//            $files = Filesystem :: get_directory_content($temp_directory, Filesystem :: LIST_FILES, false);
//            
//            if (in_array($file_name, $files))
//            {
//                $participant_data = unserialize(file_get_contents($path));
//            }
//            else
//            {
                
                $level = $participant[self :: CASE_TEMPLATE_LEVEL];
                $participant_data[$this->variable_list[self :: CONTEXT_TYPE . $level]] = $participant[self :: CASE_TEMPLATE_ID];
                $participant_data[$this->variable_list[self :: CONTEXT . $level]] = $participant[self :: CASE_CONTEXT_ID];
                
                foreach ($questions[$participant[self :: CASE_TEMPLATE_ID]] as $question)
                {
                    
                    $question_id = $question->get_id();
                    
                    $answer = $this->answer_matrix[$participant_id][$question_id];
                    
                    
                    
//                    $conditions = array();
//                    $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $participant_id);
//                    $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $question->get_id());
//                    $condition = new AndCondition($conditions);
//                    $tracker_count = Tracker :: count_data('survey_question_answer_tracker', SurveyManager :: APPLICATION_NAME, $condition);
//                    
                    if (isset($answer))
                    {
//                        $trackers = Tracker :: get_data('survey_question_answer_tracker', SurveyManager :: APPLICATION_NAME, $condition);
//                        $tracker = $trackers->next_result();
//                        $answer = $tracker->get_answer();
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
                                    $participant_data[$this->variable_list[$question_id][$key]] = self :: MISSING_VALUE;
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
                                            $participant_data[$this->variable_list[$question_id][$key]] = $match_key;
                                        
                                        }
                                        else
                                        {
                                            $participant_data[$this->variable_list[$question_id][$key]] = $match;
                                        
                                        }
                                    }
                                }
                            }
                            
                            break;
                        case SurveyMultipleChoiceQuestion :: get_type_name() :
                            
                            if ($no_answer)
                            {
                                $participant_data[$this->variable_list[$question_id][0]] = self :: MISSING_VALUE;
                            }
                            else
                            {
                                foreach ($answer as $key => $option)
                                {
                                    if ($question->get_answer_type() == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
                                    {
                                        $participant_data[$this->variable_list[$question_id][0]] = $key;
                                    }
                                    else
                                    {
                                        $participant_data[$this->variable_list[$question_id][0]] = $option;
                                    }
                                }
                            }
                            
                            break;
                    }
                
                }
//                Filesystem :: write_to_file($path, serialize($participant_data), false);
//            }
            
            $data = array_merge($data, $participant_data);
        }
        
        $vars_set = array_keys($data);
        $vars_not_set = array_diff(array_keys($this->variable_encodings), $vars_set);
        
        foreach ($vars_not_set as $var)
        {
            $data[$var] = self :: MISSING_VALUE;
        }
        
        return $data;
    }

    private function create_answer_matrix()
    {
    
    }

    private function get_content($path)
    {
        
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
        

        $cases = $this->get_cases($this->get_publication_ids());
        
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
            $content[] = '';
            //            dump($content);
            $content = implode("\n", $content);
            //            dump($content);
            Filesystem :: write_to_file($path, $content, true);
        }
        
        //                exit();
        

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
                $value_label[] = $key . ' "' . $value . '"';
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
            $variable_label = $label . ' "' . $encoding[self :: VARIABLE_LABEL] . '"';
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

    private function get_context_name_values($template_id)
    {
        
        $dm = SurveyContextDataManager :: get_instance();
        $template = $dm->retrieve_survey_context_template($template_id);
        $context_name_values = array();
        //    	dump($template->get_context_type());
        $contexts = $dm->retrieve_survey_contexts($template->get_context_type());
        while ($context = $contexts->next_result())
        {
            $context_value = array();
            $context_value[self :: CONTEXT_NAME_VALUE] = $context->get_name();
            $context_value[self :: CONTEXT_NAME_ID] = $context->get_id();
            $context_name_values[] = $context_value;
        }
        
        return $context_name_values;
    }

    private function get_context_variables($ids)
    {
        
        $this->surveys = array();
        
        $context_templates = array();
        
        foreach ($ids as $id)
        {
            $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
            $survey = $survey_publication->get_publication_object();
            $this->surveys[] = $survey;
            $context_templates[$survey->get_context_template_id()] = $survey->get_context_template();
        
        }
        
        $context_variables = array();
        
        $this->template_levels = array();
        //        $context_types = array();
        

        foreach ($context_templates as $template)
        {
            
            $context_type = array();
            $context_type[self :: CONTEXT_TYPE_NAME_VALUE] = $template->get_name();
            $context_type[self :: CONTEXT_TYPE_NAME_ID] = $template->get_id();
            
            //            $context_types[$template->get_id()] = $context_type;
            $level = $template->count_parents();
            $this->template_levels[$template->get_id()] = $level;
            $context_variables[$level][self :: CONTEXT_TYPE_NAME_VALUES][$template->get_id()] = $context_type;
            
            $children = $template->get_children(false);
            
            while ($child = $children->next_result())
            {
                
                $context_type = array();
                $context_type[self :: CONTEXT_TYPE_NAME_VALUE] = $child->get_name();
                $context_type[self :: CONTEXT_TYPE_NAME_ID] = $child->get_id();
                
                //                $context_types[$child->get_id()] = $context_type;
                $level = $child->count_parents();
                $this->template_levels[$child->get_id()] = $level;
                $context_variables[$level][self :: CONTEXT_TYPE_NAME_VALUES][$child->get_id()] = $context_type;
                
                $parent_child_context_ids[$template->get_id()] = $child->get_id();
            }
        
        }
        
        foreach ($context_variables as $level => $context_variable)
        {
            $context_names = array();
            foreach ($context_variable[self :: CONTEXT_TYPE_NAME_VALUES] as $template_id => $values)
            {
                $context_names = array_merge($context_names, $this->get_context_name_values($template_id));
            }
            $context_variables[$level][self :: CONTEXT_NAME_VALUES] = $context_names;
        
        }
        
        return $context_variables;
    }

    private function get_cases()
    {
        
        $started_status = array(SurveyParticipantTracker :: STATUS_STARTED, SurveyParticipantTracker :: STATUS_FINISHED);
        
        $conditions = array();
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_STATUS, $started_status);
        //        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID, $parent_child_context_ids);
        $conditions[] = new InCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->get_publication_ids());
//        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, 1221);
        $condition = new AndCondition($conditions);
        
        $count = Tracker :: count_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        //        dump($count);
        

        $trackers = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        $cases = array();
        $case_id = 1;
        
        $level_count = count(array_unique($this->template_levels));
        
        $participants_ids = array();
        
        while ($tracker = $trackers->next_result())
        {
            $case = array();
            $case[self :: CASE_USER_ID] = $tracker->get_user_id();
            
            $participants = array();
            $participant = array();
            $template_id = $tracker->get_context_template_id();
            $participant[self :: CASE_TEMPLATE_LEVEL] = $this->template_levels[$template_id];
            $participant[self :: CASE_TEMPLATE_ID] = $template_id;
            $participant[self :: CASE_CONTEXT_ID] = $tracker->get_context_id();
            $participants[$tracker->get_id()] = $participant;
            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $tracker->get_parent_id());
            $participants_ids[] = $tracker->get_id();
            $parent_tracker = Tracker :: get_data('survey_participant_tracker', SurveyManager :: APPLICATION_NAME, $condition)->next_result();
            if (isset($parent_tracker))
            {
                $participant = array();
                $template_id = $parent_tracker->get_context_template_id();
                $participant[self :: CASE_TEMPLATE_LEVEL] = $this->template_levels[$template_id];
                $participant[self :: CASE_TEMPLATE_ID] = $template_id;
                $participant[self :: CASE_CONTEXT_ID] = $parent_tracker->get_context_id();
                $participants[$parent_tracker->get_id()] = $participant;
                $participants_ids[] = $parent_tracker->get_id();
            }
            
            if (count($participants) == $level_count)
            {
                $case[self :: CASE_PARTICIPANTS] = $participants;
                $cases[$case_id] = $case;
                $case_id ++;
            }
            
        //            dump(memory_get_usage(true));
        //            if ($case_id == 100)
        //            {
        //                break;
        //                                }
        //        
        //
        }
        
        $participants_ids = array_unique($participants_ids);
               
        $condition = new InCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $participants_ids);
        $tracker_count = Tracker :: count_data('survey_question_answer_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        
//        dump($tracker_count);
        
        $this->answer_matrix = array();
        
        $trackers = Tracker :: get_data('survey_question_answer_tracker', SurveyManager :: APPLICATION_NAME, $condition);
        while ($tracker = $trackers->next_result()) {
        	$this->answer_matrix[$tracker->get_survey_participant_id()][$tracker->get_question_cid()] = $tracker->get_answer();
//        	dump(memory_get_usage(true)/1024);
        }
        
//        dump($this->answer_matrix);
        
//        exit;
//        
//        
//        
//        dump($cases);
//        exit();
        
        //        dump($this->template_levels);
        //        
        //        dump($cases);
        //        
        //        exit();
        return $cases;
    }

    private function set_publication_ids($ids)
    {
        $this->publication_ids = $ids;
    }

    private function get_publication_ids()
    {
        return $this->publication_ids;
    }
}

?>