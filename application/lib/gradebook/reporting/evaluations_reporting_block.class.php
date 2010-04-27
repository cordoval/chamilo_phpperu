<?php
abstract class EvaluationsReportingBlock extends ReportingBlock
{
	public function get_data_manager()
	{
		return SurveyDataManager::get_instance();
	}
	
	public function get_publication_id()
	{
		dump($this->get_parent());exit;
		return $this->get_parent()->get_parameter(GradebookManager :: PARAM_PUBLICATION_ID);
	}
	
	public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter ::DISPLAY_TABLE] = Translation :: get('Table');
//        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
//        $modes[ReportingChartFormatter ::DISPLAY_BAR ] = Translation :: get('Chart:Bar');
//        $modes[ReportingChartFormatter ::DISPLAY_LINE] = Translation :: get('Chart:Line');
//        $modes[ReportingChartFormatter ::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }
}
?>