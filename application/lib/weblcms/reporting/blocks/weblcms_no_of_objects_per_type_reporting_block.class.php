<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsNoOfObjectsPerTypeReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		$rdm = RepositoryDataManager :: get_instance();
        $list = $rdm->get_registered_types();
        foreach ($list as $key => $value)
        {
            $arr[$value][0] = 0;
        }

        $list = $rdm->retrieve_content_objects();
        while ($content_object = $list->next_result())
        {
            $arr[$content_object->get_type()][0] ++;
        }

        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get(Utilities :: underscores_to_camelcase($key))] = $arr[$key];
            unset($arr[$key]);
        }

        $description[0] = Translation :: get('Object');
        return Reporting :: getSerieArray($arr, $description);
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter::DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
	}
}
?>