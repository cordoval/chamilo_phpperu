<?php
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';

class WeblcmsLastAccessToToolsPlatformReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('Tool'), Translation :: get('Clicks'), Translation :: get('Publications')));
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();

        $tools = $wdm->get_all_course_modules();
        foreach ($tools as $name)
        {
            $image = '<img src="' . Theme :: get_image_path('weblcms') . 'tool_' . $name . '.png" style="vertical-align: middle;" /> ';
            $tool = Translation :: get(Utilities :: underscores_to_camelcase($name));
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*tool=' . $name . '*');

            $trackerdata = $tracker->retrieve_tracker_items($condition);
            $url = Reporting :: get_weblcms_reporting_url('ToolPublicationsDetailReportingTemplate', $params);
            $link = '<a href="' . $url . '">' . Translation :: get('ViewPublications') . '</a>';
            $reporting_data->add_category($tool);
            $reporting_data->add_data_category_row($tool, Translation :: get('Tool'), $image);
			$reporting_data->add_data_category_row($tool, Translation :: get('Clicks'), count($trackerdata));
			$reporting_data->add_data_category_row($tool, Translation :: get('Publications'), $link);
        }
		
		return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        //$modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>