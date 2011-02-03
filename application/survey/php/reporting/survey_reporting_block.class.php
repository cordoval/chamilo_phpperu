<?php 
namespace application\survey;

use reporting\ReportingBlock;
use reporting\ReportingFormatter;
use reporting\ReportingChartFormatter;
use common\libraries\Translation;

//require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class SurveyReportingBlock extends ReportingBlock
{
	
	function get_filter_parameters(){
		return $this->get_parent()->get_filter_parameters();
	}
	
	public function get_data_manager()
	{
		return SurveyDataManager::get_instance();
	}
	
	public function get_available_displaymodes()
    {
        $modes = array();
//        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter ::DISPLAY_TABLE] = Translation :: get('Table');
//        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
//        $modes[ReportingChartFormatter ::DISPLAY_BAR ] = Translation :: get('Chart:Bar');
//        $modes[ReportingChartFormatter ::DISPLAY_LINE] = Translation :: get('Chart:Line');
//        $modes[ReportingChartFormatter ::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }
    
}
?>