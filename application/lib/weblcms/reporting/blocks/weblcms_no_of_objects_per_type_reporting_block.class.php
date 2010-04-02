<?php
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';

class WeblcmsNoOfObjectsPerTypeReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		/*$rdm = RepositoryDataManager :: get_instance();
        $list = $rdm->get_registered_types();
        foreach ($list as $key => $value)
        {
            $arr[$value] = 0;
        }

        $list = $rdm->retrieve_content_objects();
        while ($content_object = $list->next_result())
        {
            $arr[$content_object->get_type()] ++;
        }

        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get(Utilities :: underscores_to_camelcase($key))] = $arr[$key];
            unset($arr[$key]);
        }

        $description[0] = Translation :: get('Object');
        
        $reporting_data->set_categories(array($list));
        $reporting_data->set_rows(array(Translation :: get('Object')));

        $reporting_data->add_data_category_row($content_object->get_type(), Translation :: get('Object'), $arr[$content_object->get_type()]);
        */
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	function get_application()
	{
		return WeblcmsManager::APPLICATION_NAME;
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