<?php

namespace application\assessment;

use common\libraries\EqualityCondition;
use reporting\ReportingData;
use common\libraries\Translation;
use user\UserDataManager;
use repository\content_object\hotpotatoes\Hotpotatoes;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use repository\content_object\assessment\Assessment;
use common\libraries\DatetimeUtilities;
/**
 * $Id: reporting_assessment.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.reporting
 */
/**
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../lib/assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database_assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/../lib/assessment_manager/assessment_manager.class.php';
require_once dirname(__FILE__) . '/../trackers/assessment_assessment_attempts_tracker.class.php';

class ReportingAssessment
{

    function ReportingAssessment()
    {
    
    }

    public static function getAssessmentAttempts($params)
    {	
        $aid = $params[AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION];
        $url = $params['url'];
        $results_export_url = $params['results_export_url'];
        
        $dummy = new AssessmentAssessmentAttemptsTracker();
        $condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $aid);
        $trackers = $dummy->retrieve_tracker_items($condition);
        $pub = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($aid);
        $assessment = $pub->get_publication_object();
       	
        $reporting_data = new ReportingData();
		$set_rows = $reporting_data->set_rows(array(Translation :: get('User'), Translation :: get('Date'), Translation :: get('TotalScore'), Translation :: get('Action')));

        foreach ($trackers as $index => $tracker)
        {
        	$reporting_data->add_category($index);
            $user = UserDataManager :: get_instance()->retrieve_user($tracker->get_user_id());
            $reporting_data->add_data_category_row($index, Translation :: get('User'), $user->get_fullname());
            $reporting_data->add_data_category_row($index, Translation :: get('Date'), DatetimeUtilities :: format_locale_date(null, $tracker->get_date()));
            $reporting_data->add_data_category_row($index, Translation :: get('TotalScore'), $tracker->get_total_score() . '%');
            $toolbar = new Toolbar();
            if (!array_key_exists('export',$params))
            {
	            if ($assessment->get_type() != Hotpotatoes :: get_type_name())
	            {
	            	$toolbar->add_item(new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $url . '&details=' . $tracker->get_id(), ToolbarItem :: DISPLAY_ICON ));
	                $toolbar->add_item(new ToolbarItem(Translation :: get('ExportResults'), Theme :: get_common_image_path() . 'action_export.png', $results_export_url . '&tid=' . $tracker->get_id(), ToolbarItem :: DISPLAY_ICON ));
	                
	            }
	            
	            $toolbar->add_item(new ToolbarItem(Translation :: get('DeleteResults'), Theme :: get_common_image_path() . 'action_delete.png', $url . '&delete=tid_' . $tracker->get_id(), ToolbarItem :: DISPLAY_ICON, true ));
            $reporting_data->add_data_category_row($index, Translation :: get('Action'), $toolbar->as_html());
            }
        }
        $reporting_data->hide_categories();
        return $reporting_data;
    }

    public static function getSummaryAssessmentAttempts($params)
    {
        $data = array();
        $category = $params['category'];
        $url = $params['url'];
        
        $adm = AssessmentDataManager :: get_instance();
        $condition = new EqualityCondition(AssessmentPublication :: PROPERTY_CATEGORY, $category);
        $publications = $adm->retrieve_assessment_publications($condition);
        $dummy = new AssessmentAssessmentAttemptsTracker();
		
        $reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('Type'), Translation :: get('Title'), Translation :: get('TimesTaken'), Translation :: get('AverageScore')));
		if (!array_key_exists('export',$params))
        {
        	$reporting_data->add_row(Translation :: get('Action'));
        }
        while ($publication = $publications->next_result())
        {
            $lo = $publication->get_publication_object();
            $type = $lo->get_type();
            
        	$reporting_data->add_category($lo->get_id());
            if ($type == Assessment :: get_type_name())
            {
                $type = $lo->get_assessment_type();
            }
            
            $reporting_data->add_data_category_row($lo->get_id(), Translation :: get('Type'), Translation :: get($type));
            $reporting_data->add_data_category_row($lo->get_id(), Translation :: get('Title'), $lo->get_title());
            $reporting_data->add_data_category_row($lo->get_id(), Translation :: get('TimesTaken'), $dummy->get_times_taken($publication));
            $reporting_data->add_data_category_row($lo->get_id(), Translation :: get('AverageScore'),$dummy->get_average_score($publication) . '%');
            
            $toolbar = new Toolbar();
            if (!array_key_exists('export',$params))
            {
            	$toolbar->add_item(new ToolbarItem(Translation :: get('ViewResults', Theme :: get_common_image_path() . 'action_view_results.png'), $url . '&' . AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION . '=' . $publication->get_id(), ToolbarItem::DISPLAY_ICON));
	            
            	$toolbar->add_item(new ToolbarItem(Translation :: get('DeleteResults', Theme :: get_common_image_path() . 'action_delete.png'), $url . '&delete=aid_' . $publication->get_id(), ToolbarItem::DISPLAY_ICON, true));
	            
	            $reporting_data->add_data_category_row($lo->get_id(), Translation :: get('Action'), $toolbar->as_html());
            }
        }
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }

}
?>