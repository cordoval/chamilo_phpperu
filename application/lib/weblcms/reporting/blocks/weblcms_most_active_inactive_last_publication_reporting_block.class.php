<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsMostActiveInactiveLastPublicationReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		$wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->retrieve_courses(null, null, null, $params['order_by']);

        $arr[Translation :: get('Past24hr')][0] = 0;
        $arr[Translation :: get('PastWeek')][0] = 0;
        $arr[Translation :: get('PastMonth')][0] = 0;
        $arr[Translation :: get('PastYear')][0] = 0;
        $arr[Translation :: get('NothingPublished')][0] = 0;

        while ($course = $courses->next_result())
        {
            $lastpublication = 0;

            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
            $publications = $datamanager->retrieve_content_object_publications_new($condition);

            while ($publication = $publications->next_result())
            {
                $lastpublication = $publication->get_modified_date();
                $lastpublication = date('Y-m-d G:i:s', $lastpublication);
            }

            if ($lastpublication == 0)
            {
                $arr[Translation :: get('NothingPublished')][0] ++;
            }
            else
                if (strtotime($lastpublication) > time() - 86400)
                {
                    $arr[Translation :: get('Past24hr')][0] ++;
                }
                else
                    if (strtotime($lastpublication) > time() - 604800)
                    {
                        $arr[Translation :: get('PastWeek')][0] ++;
                    }
                    else
                        if (strtotime($lastpublication) > time() - 18144000)
                        {
                            $arr[Translation :: get('PastMonth')][0] ++;
                        }
                        else
                            if (strtotime($lastpublication) > time() - 31536000)
                            {
                                $arr[Translation :: get('PastYear')][0] ++;
                            }
                            else
                            {
                                $arr[Translation :: get('MoreThenOneYear')][0] ++;
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
