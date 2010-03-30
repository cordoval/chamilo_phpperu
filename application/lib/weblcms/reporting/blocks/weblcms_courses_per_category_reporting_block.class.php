<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsCoursesPerCategoryReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		$wdm = WeblcmsDataManager :: get_instance();

        $categories = $wdm->retrieve_course_categories();

        while ($category = $categories->next_result())
        {
            $arr[$category->get_name()][0] = 0;
            $condition = new EqualityCondition(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID, $category->get_id());
            $courses = $wdm->retrieve_courses($condition);
            while ($course = $courses->next_result())
            {
                $arr[$category->get_name()][0] ++;
            }
        }

        return Reporting :: getSerieArray($arr);
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