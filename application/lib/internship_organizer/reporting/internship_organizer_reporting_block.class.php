<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class InternshipOrganizerReportingBlock extends ReportingBlock
{
	public function count_data()
	{}
	
	public function retrieve_data()
	{}
	
//	function get_survey_publication_id()
//	{
//		return $this->get_parent()->get_parameter(SurveyManager::PARAM_SURVEY_PUBLICATION);	
//	}
//	
//	function get_survey_question_id()
//	{
//		return $this->get_parent()->get_parameter(SurveyManager::PARAM_SURVEY_QUESTION);	
//	}
	
	public function get_data_manager()
	{
		return InternshipOrganizerDataManager::get_instance();
	}
	
	public function get_available_displaymodes()
    {
        $modes = array();
//        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter ::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        $modes[ReportingChartFormatter ::DISPLAY_BAR ] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter ::DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter ::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }
}
?>