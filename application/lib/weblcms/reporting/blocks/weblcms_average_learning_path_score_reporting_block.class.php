<?php
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsAverageLearningPathScoreReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('LearningPath')));

		$course_id = $this->get_course_id();
        $wdm = WeblcmsDataManager :: get_instance();

        $course = $wdm->retrieve_course($course_id);
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'learning_path');
        $lops = $wdm->retrieve_content_object_publications_new($condition, $params['order_by']);

        while ($lop = $lops->next_result())
        {
            $lpo = $lop->get_content_object();
            //$arr[$lpo->get_title()] = 0;
            $reporting_data->add_data_category_row( Translation :: get('LearningPath'), Translation :: get('Average'), $arr[$lpo->get_title()]);
        }

        //$datadescription[0] = Translation :: get('LearningPath');
        //$datadescription[1] = Translation :: get('Average');

        //$reporting_data->add_category($learn);

        
        
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
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>