<?php
/*
 * This component is responsible to retrive all the reporting data for a survey
 */

class SurveyManagerSurveyExcelExporterComponent extends ReportingBlock
{
	const TEMPLATE_SURVEY_REPORTING = 'survey_reporting_template';

	/**
	 * Runs this component and displays its output.
	 */
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
		
		dump($pages);
		
		
		 //RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
	
		
	//	$survey_id->
//		$question_id = Request :: get(SurveyManager :: PARAM_SURVEY_QUESTION);
//		$this->set_parameter(SurveyManager :: PARAM_SURVEY_QUESTION, $question_id);
//
//		$trail = new BreadcrumbTrail();
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
}


?>