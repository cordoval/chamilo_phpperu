<?php
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsMostActiveInactiveLastPublicationReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->retrieve_courses();

        $arr[Translation :: get('Past24hr')] = 0;
        $arr[Translation :: get('PastWeek')] = 0;
        $arr[Translation :: get('PastMonth')] = 0;
        $arr[Translation :: get('PastYear')] = 0;
        $arr[Translation :: get('NothingPublished')] = 0;
        $arr[Translation :: get('MoreThenOneYear')] = 0;

        while ($course = $courses->next_result())
        {
            $lastpublication = 0;

            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
            $publications = $wdm->retrieve_content_object_publications($condition);

            while ($publication = $publications->next_result())
            {
                $lastpublication = $publication->get_modified_date();
                $lastpublication = date('Y-m-d G:i:s', $lastpublication);
            }

            if ($lastpublication == 0)
            {
                $arr[Translation :: get('NothingPublished')] ++;
            }
            else
                if (strtotime($lastpublication) > time() - 86400)
                {
                    $arr[Translation :: get('Past24hr')] ++;
                }
                else
                    if (strtotime($lastpublication) > time() - 604800)
                    {
                        $arr[Translation :: get('PastWeek')] ++;
                    }
                    else
                        if (strtotime($lastpublication) > time() - 18144000)
                        {
                            $arr[Translation :: get('PastMonth')] ++;
                        }
                        else
                            if (strtotime($lastpublication) > time() - 31536000)
                            {
                                $arr[Translation :: get('PastYear')] ++;
                            }
                            else
                            {
                                $arr[Translation :: get('MoreThenOneYear')] ++;
                            }
        }
        $reporting_data->set_categories(array(Translation :: get('Past24hr'), Translation :: get('PastWeek'), Translation :: get('PastMonth'), Translation :: get('PastYear'), Translation :: get('MoreThenOneYear'), Translation :: get('NothingPublished')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('Past24hr'), Translation :: get('count'), $arr[Translation :: get('Past24hr')]);
		$reporting_data->add_data_category_row(Translation :: get('PastWeek'), Translation :: get('count'), $arr[Translation :: get('PastWeek')]);
	    $reporting_data->add_data_category_row(Translation :: get('PastMonth'), Translation :: get('count'), $arr[Translation :: get('PastMonth')]);
		$reporting_data->add_data_category_row(Translation :: get('PastYear'), Translation :: get('count'), $arr[Translation :: get('PastYear')]);
	    $reporting_data->add_data_category_row(Translation :: get('NothingPublished'), Translation :: get('count'), $arr[Translation :: get('NothingPublished')]);
	    $reporting_data->add_data_category_row(Translation :: get('MoreThenOneYear'), Translation :: get('count'), $arr[Translation :: get('MoreThenOneYear')]);
				
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
        //$modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>