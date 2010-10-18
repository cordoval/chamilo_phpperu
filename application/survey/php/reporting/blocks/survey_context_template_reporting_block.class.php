<?php

/**
 * $Id: survey_context_template_reporting_block.class.php $Shoira Mukhsinova
 * @package application/lib/survey/reporting/blocks
 */
require_once dirname ( __FILE__ ) . '/../survey_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../survey_manager/survey_manager.class.php';


class SurveyContextTemplateReportingBlock extends SurveyReportingBlock {



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
	
		while ( $tracker = $trackers->next_result () ) {
			$tr_template_id = $tracker->get_context_template_id();
			for ($i =0; $i<$size; $i++){
				switch ($tr_template_id){
					case $switch_case[$i]:
						$count_templates[$i] ++;
						break;			
				}
				
			}
		}

	    $reporting_data = new ReportingData();
		$reporting_data->set_categories ($categories);
		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );
		
		
 	   for ($i=0; $i<$size; $i++){

		$reporting_data->add_data_category_row ($categories[$i],Translation::get ( 'Count' ),$count_templates[$i]);
		
 	   }
	
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