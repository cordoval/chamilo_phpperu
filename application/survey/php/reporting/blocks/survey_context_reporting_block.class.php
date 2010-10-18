<?php

/**
 * $Id: survey_context_reporting_block.class.php $Shoira Mukhsinova
 * @package application/lib/survey/reporting/blocks
 */
require_once dirname ( __FILE__ ) . '/../survey_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../survey_manager/survey_manager.class.php';


class SurveyContextReportingBlock extends SurveyReportingBlock {



	public function count_data() {

		require_once (dirname ( __FILE__ ) . '/../../trackers/survey_participant_tracker.class.php');


		$publication_id = $this->get_survey_publication_id ();
		$survey_publication = SurveyDataManager::get_instance()->retrieve_survey_publication($publication_id);
		$survey = $survey_publication->get_publication_object();

		//retirive survey context templates
		$context_template = $survey->get_context_template();
		$context_template_id = $context_template->get_id();

		$condition = new EqualityCondition ( SurveyParticipantTracker::PROPERTY_SURVEY_PUBLICATION_ID, $publication_id );
		$tracker = new SurveyParticipantTracker ();
		$trackers = $tracker->retrieve_tracker_items_result_set ( $condition );


		$context_template_ids = array();
		$categories = array();

		$categories[] = $context_template->get_name();
		$context_template_ids[] = $context_template_id;

		if ($context_template->has_children()){
			$context_template_children = $context_template->get_children(false);
			while($child = $context_template_children->next_result()){
					
				$context_template_ids[] =  $child->get_id();
				$categories[] = $child->get_name();
			}
				
		}

		$size = count($context_template_ids);
		$switch_case = array();
		$count_templates = array();
		for ($i =0; $i<$size; $i++){
			$switch_case[$i] = $context_template_ids[$i];
			$count_templates[$i] = 0;
		}
		$context_names = array();
		$context_id = array();
		$count_context_id = array();
		while ( $tracker = $trackers->next_result () ) {
			$tr_template_id = $tracker->get_context_template_id();
			$tr_context_id = $tracker->get_context_id();
			$context_names[$tr_context_id] = $tracker->get_context_name();
			for ($i =0; $i<$size; $i++){

				$size_1 = count($context_id);
				switch ($tr_template_id){
					case $switch_case[$i]:
						$count_templates[$i] ++;

						if ($size_1 == 0){
							$context_id[] = $tr_context_id;
							$count_context_id[$tr_template_id][$tr_context_id] = 0;
						}
						else{
							$found = 0;
							foreach ($context_id as $context_exist){
								if ($context_exist == $tr_context_id){
									$found = 1;
									$count_context_id[$tr_template_id][$tr_context_id] ++;
									break;
								}
							}
							if ($found == 0){
								$context_id[] = $tr_context_id;
								$count_context_id[$tr_template_id][$tr_context_id] = 0;
							}
						}
						break;
				}

			}
		}

		//*
		$new_category = array();
		for ($k = 0; $k<$size; $k ++){
			$template_id = $context_template_ids[$k];
			//in order to display context_template_id uncomment the below row
			//$new_category[$categories[$k]] = $categories[$k];
			foreach($context_id as $context){
				if ($count_context_id[$template_id][$context] != ""){
						
					$new_category[] =$context_names[$context];

				}
			}
		}

		$reporting_data = new ReportingData();
		$reporting_data->set_categories ($new_category);
		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );

		$category_context = array();
		//loop at context_template_id's
		for ($k = 0; $k<$size; $k ++){
			
			$template_id = $context_template_ids[$k];
			$template_name = $categories[$k];
				
			//$reporting_data->add_data_category_row ($categories[$k],Translation::get ( 'Count' ),$count_templates[$k]);
				
			foreach($context_id as $context){
				if ($count_context_id[$template_id][$context] != ""){
					$categories[] =$context_names[$context];
					$category_context[$context_names[$context]] = $count_context_id[$template_id][$context];
					

				}
			}
			$qty = count($category_context);
			
			for ($s = 0; $s<$qty; $s++){

				$reporting_data->add_data_category_row ($new_category[$s],Translation::get ( 'Count' ),$category_context[$new_category[$s]]);
		        	
			}
	
		}

		

		//*/test array contents
		//dump($context_id);
		//dump($count_context_id);
		//dump($categories);
		//dump($context_template_ids);
		//dump($context_names);
		//dump($category_context);
		//	dump($count_templates);
		//	dump($switch_case);
		
		return $reporting_data;

	}

	public function retrieve_data() {
		return $this->count_data ();
	}

	function get_application() {
		return SurveyManager::APPLICATION_NAME;
	}

	public function get_available_displaymodes() {
		$modes = array ();
		$modes [ReportingFormatter::DISPLAY_TABLE] = Translation::get ( 'Table' );
		$modes [ReportingChartFormatter::DISPLAY_PIE] = Translation::get ( 'Chart:Pie' );
		$modes [ReportingChartFormatter::DISPLAY_BAR] = Translation::get ( 'Chart:Bar' );
		$modes [ReportingChartFormatter::DISPLAY_LINE] = Translation::get ( 'Chart:Line' );
		$modes [ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation::get ( 'Chart:FilledCubic' );
		return $modes;
	}
}
?>