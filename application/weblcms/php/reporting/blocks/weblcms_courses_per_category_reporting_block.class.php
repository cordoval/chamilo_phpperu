<?php
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsCoursesPerCategoryReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('count')));
		$wdm = WeblcmsDataManager :: get_instance();

        $categories = $wdm->retrieve_course_categories();

        while ($category = $categories->next_result())
        {
        	$arr[$category->get_name()] = 0;
            $condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $category->get_id());

            $reporting_data->add_category($category->get_name());
            $reporting_data->add_data_category_row($category->get_name(), Translation :: get('count'), $wdm->count_courses($condition));       
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
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>