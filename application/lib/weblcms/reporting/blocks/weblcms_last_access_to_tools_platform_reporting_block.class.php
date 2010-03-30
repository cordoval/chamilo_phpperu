<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsLastAccessToToolsPlatformReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();

        $tools = $wdm->get_all_course_modules();

        foreach ($tools as $name)
        {
            $link = '<img src="' . Theme :: get_image_path('weblcms') . 'tool_' . $name . '.png" style="vertical-align: middle;" /> ' . Translation :: get(Utilities :: underscores_to_camelcase($name));
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*tool=' . $name . '*');

            $trackerdata = $tracker->retrieve_tracker_items($condition);

            $arr[$link][] = count($trackerdata);
            $params['tool'] = $name;
            $url = Reporting :: get_weblcms_reporting_url('ToolPublicationsDetailReportingTemplate', $params);
            $arr[$link][] = '<a href="' . $url . '">' . Translation :: get('ViewPublications') . '</a>';
        }

        $description[0] = Translation :: get('Tool');
        $description[1] = Translation :: get('Clicks');
        $description[2] = Translation :: get('Publications');
        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_VERTICAL;
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
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        //$modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        //$modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>