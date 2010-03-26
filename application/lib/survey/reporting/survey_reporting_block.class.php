<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class SurveyReportingBlock extends ReportingBlock
{
	public function count_data()
	{}
	
	public function retrieve_data()
	{}
	
	public function get_data_manager()
	{
		return SurveyDataManager::get_instance();
	}
	
	public function get_available_displaymodes()
    {
        $modes = array();
        $modes["Text"] = Translation :: get('Text');
        $modes["Table"] = Translation :: get('Table');
        $modes["Chart:Pie"] = Translation :: get('Chart:Pie');
        $modes["Chart:Bar"] = Translation :: get('Chart:Bar');
        $modes["Chart:Line"] = Translation :: get('Chart:Line');
        $modes["Chart:FilledCubic"] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }
}
?>