<?php
require_once (Path :: get_reporting_path(). 'lib/reporting_data.class.php');


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

	private function get_file_name(){
		$survey_publication_id = Request::get(SurveyManager::PARAM_SURVEY_PUBLICATION);
		$survey_publication = SurveyDataManager::get_instance()->retrieve_survey_publication($survey_publication_id);
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

		$survey_publication_id = Request::get(SurveyManager::PARAM_SURVEY_PUBLICATION);
		$survey_publication = SurveyDataManager::get_instance()->retrieve_survey_publication($survey_publication_id);
		$survey = $survey_publication->get_publication_object();
		

		$pages = array();
		$pages = $survey->get_pages();

		//dump($pages);
		//$this->display_header();
	   $reporting_data_all = array();
		foreach ($pages as $page){
			//dump ($page->get_questions());
			$questions = $page->get_questions();
            
			foreach ($questions as $question){
				//echo $question->get_title();
				//echo $question->get_description();
				$reporting_data_question = array();
				array_push($reporting_data_question,  $question->get_title());	
				array_push($reporting_data_question,  $question->get_description());
				
				$reporting_data = $this->create_reporting_data($question);
				array_push($reporting_data_question,  $reporting_data);
				
				
				
				array_push($reporting_data_all, $reporting_data_question );
				
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
		//dump($reporting_data_all);
		$this->save_excel($reporting_data_all);
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
	
	
	public function save_excel($reporting_data){
		///send to browser for download
		$export = Export :: factory('excel', $reporting_data);
        $export->set_filename($this->get_file_name());
    	$export->send_to_browser();
    	
		$export = Export :: factory('excel', $reporting_data);
       $export->set_filename($this->get_file_name());
    	return $export->render_data();
	}
	
	public function convert_reporting_data($reporting_data)
	{
		$table_data = array();
		foreach($reporting_data->get_categories() as $category_id => $category_name)
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