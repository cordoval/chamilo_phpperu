<?php
require_once (Path :: get_reporting_path() . 'lib/reporting_data.class.php');
require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

/*
 * This component is responsible to retrive all the reporting data for a survey
 */

class SurveyManagerSurveyExcelExporterComponent extends SurveyManager
{
    const TEMPLATE_SURVEY_REPORTING = 'survey_reporting_template';
    const NO_ANSWER = 'noAnswer';
    const COUNT = 'count';
        

    /**
     * Runs this component and displays its output.
     */
    
    private function get_file_name()
    {
        $survey_publication_id = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_publication_id);
        $survey = $survey_publication->get_publication_object();
        return $survey->get_title();
    }

    private function create_reporting_data($question)
    {
        
        //retrieve the answer trackers
        $condition = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $question->get_id());
        $tracker = new SurveyQuestionAnswerTracker();
        $trackers = $tracker->retrieve_tracker_items_result_set($condition);
        
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
                $opts = $question->get_options();
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
            default :
                ;
                break;
        }
        
        return $reporting_data;
    }

    function run()
    {
        
        /**
         * Get the the survey ID and loop over all the pages. Inside the pages loop over all the
         * questions that are contained in the page.
         */
        
        $survey_publication_id = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $survey_publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_publication_id);
        $survey = $survey_publication->get_publication_object();
        
        $pages = array();
        $pages = $survey->get_pages();
        
        //dump($pages);
        //$this->display_header();
        $reporting_data_all = array();
        foreach ($pages as $page)
        {
            //dump ($page->get_questions());
            $questions = $page->get_questions();
            
            foreach ($questions as $question)
            {
                //echo $question->get_title();
                //echo $question->get_description();
//                dump($question);
            	
            	$reporting_data_question = array();
                $reporting_data_question[] = $question->get_title();
                $reporting_data_question[] = $question->get_description();
                
                $reporting_data = $this->create_reporting_data($question);
                $converted_reporting_data = $this->convert_reporting_data($reporting_data);
                
                $reporting_data_question[] = $reporting_data;
                
                $reporting_data_all[] = $reporting_data_question;
                
//                $reporting_data_all[] = $converted_reporting_data;
                
                
            //				$table = new SortableTableFromArray($this->convert_reporting_data($reporting_data), null, 20, 'table_' . $question->get_id());
            //
            //				$j = 0; 
            //				if ($reporting_data->is_categories_visible())
            //				{
            //					$table->set_header(0, '', false);
            //					$j++;
            //				}
            //
            //				foreach($reporting_data->get_rows() as $row)
            //				{
            //					$table->set_header($j, $row);
            //					$j++;
            //				}
            //				echo $table->toHTML();
            

            }
        
        }
//        		dump($reporting_data_all);
//        		exit;
$this->render_data($reporting_data_all);
//        $this->save_excel($reporting_data_all);
        //	$this->display_footer();
    

    //RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
    

    //	$survey_id->
    //		$question_id = Request :: get(SurveyManager :: PARAM_SURVEY_QUESTION);
    //		$this->set_parameter(SurveyManager :: PARAM_SURVEY_QUESTION, $question_id);
    //
    //		$trail = BreadcrumbTrail :: get_instance();
    //		$trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
    //		$trail->add_help('survey reporting');
    //
    //		$rtv = new ReportingViewer($this);
    //		$rtv->add_template_by_name(self :: TEMPLATE_SURVEY_REPORTING, SurveyManager :: APPLICATION_NAME);
    //		$rtv->set_breadcrumb_trail($trail);
    //		$rtv->show_all_blocks();
    //
    //		$rtv->run();
    }

    public function save_excel($reporting_data)
    {
        ///send to browser for download
        $export = Export :: factory('excel', $reporting_data);
        $export->set_filename($this->get_file_name());
        $export->send_to_browser();
        
    //		$export = Export :: factory('excel', $reporting_data);
    //       $export->set_filename($this->get_file_name());
    //    	return $export->render_data();
    }

    public function render_data($data)
    {
        $excel = new PHPExcel();
        
//        $data = $this->get_data();
        $letters = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z');
        
        $i = 0;
        $cell_letter = 0;
        $cell_number = 1;
        
        $excel->setActiveSheetIndex(0);
        
        if (is_array($data))
        {
            foreach ($data as $block_data)
            {
                $block_title = $block_data[0];
                $block_description = $block_data[1];
                $block_content_data = $block_data[2];
                
                $cell_letter = 0;
                $cell_number = $cell_number + 2;
                $excel->getActiveSheet()->setCellValue($letters[$cell_letter] . $cell_number, strip_tags(html_entity_decode($block_title)));
                $excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(60);
                $this->wrap_text($excel, $letters[$cell_letter] . $cell_number);
                ++ $cell_number;
                $excel->getActiveSheet()->setCellValue($letters[$cell_letter] . $cell_number, trim(html_entity_decode(strip_tags($block_description))));
                
                if ($block_description != "")
                {
                    $this->wrap_text($excel, $letters[$cell_letter] . $cell_number);
                }
                
                ++ $cell_number;
                //(matrix question) rows
                foreach ($block_content_data->get_rows() as $row_id => $row_name)
                {
                    //	dump($row_name);
                    $cell_letter ++;
                    $excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(15);
                    //$this->wrap_text($excel,$letters[$cell_letter].$cell_number);
                    $excel->getActiveSheet()->setCellValue($letters[$cell_letter] . $cell_number, trim(html_entity_decode(strip_tags($row_name))));
                
                }
                foreach ($block_content_data->get_categories() as $category_id => $category_name)
                {
                    $cell_letter = 0;
                    ++ $cell_number;
                    $excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(50);
                    $excel->getActiveSheet()->setCellValue($letters[$cell_letter] . $cell_number, trim(html_entity_decode(strip_tags($category_name))));
                    $this->wrap_text($excel, $letters[$cell_letter] . $cell_number);
                    foreach ($block_content_data->get_rows() as $row_id => $row_name)
                    {
                        $cell_letter ++;
                        $excel->getActiveSheet()->setCellValue($letters[$cell_letter] . $cell_number, $block_content_data->get_data_category_row($category_id, $row_id));
                    }
                    $i ++;
                }
            
            }
        
        }
       
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->get_file_name() . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory :: createWriter($excel, 'Excel2007');
        return $objWriter->save('php://output');
        
        $excel->disconnectWorksheets();
        unset($excel);
    
    }

    function wrap_text($excel, $cell)
    {
        //$excel->getActiveSheet()->getStyle($cell)->getAlignment()->setWidth(20);
        $excel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
    
    }

    public function convert_reporting_data($reporting_data)
    {
        $table_data = array();
        foreach ($reporting_data->get_categories() as $category_id => $category_name)
        {
            $category_array = array();
            if ($reporting_data->is_categories_visible())
            {
                $category_array[] = $category_name;
            }
            foreach ($reporting_data->get_rows() as $row_id => $row_name)
            {
                $category_array[] = $reporting_data->get_data_category_row($category_id, $row_id);
            }
            $table_data[] = $category_array;
        }
        return $table_data;
    }
}

?>