<?php
namespace application\weblcms\tool\learning_path;

use repository\content_object\learning_path;

use application\weblcms;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

use repository\ComplexDisplay;
use repository\content_object\learning_path\LearningPathComplexDisplaySupport;
use repository\content_object\learning_path\LearningPathDisplay;

use application\weblcms\ToolComponent;
use application\weblcms\Tool;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsManager;
use application\weblcms\WeblcmsLpAttemptTracker;
use application\weblcms\WeblcmsLpiAttemptTracker;

use tracking\Event;

require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_lp_attempt_tracker.class.php';
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_lpi_attempt_tracker.class.php';
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_lpi_attempt_objective_tracker.class.php';
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_learning_path_question_attempts_tracker.class.php';
require_once Path :: get_repository_content_object_path() . 'learning_path/php/display/learning_path_complex_display_support.class.php';

class LearningPathToolTestComponent extends LearningPathTool implements LearningPathComplexDisplaySupport
{
    
    private $publication;

    function run()
    {
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);
        
        $this->publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
        
        ComplexDisplay :: launch($this->publication->get_content_object()->get_type(), $this);
    }

    function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    function get_publication()
    {
        return $this->publication;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('LearningPathToolBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('LearningPathToolViewerComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

    function retrieve_learning_path_tracker()
    {
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $this->get_publication()->get_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        
        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $learning_path_tracker = $trackers[0];
        
        if (! $learning_path_tracker)
        {
            $parameters = array();
            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_USER_ID] = $this->get_user_id();
            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID] = $this->get_course_id();
            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_LP_ID] = $this->get_publication()->get_id();
            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS] = 0;
            
            $return = Event :: trigger('attempt_learning_path', WeblcmsManager :: APPLICATION_NAME, $parameters);
            $learning_path_tracker = $return[0];
        }
        
        return $learning_path_tracker;
    }

    function retrieve_tracker_items($learning_path_tracker)
    {
        $learning_path_item_attempt_data = array();
        
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $learning_path_tracker->get_id());
        
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        
        foreach ($trackers as $tracker)
        {
            $item_id = $tracker->get_lp_item_id();
            
            if (! $learning_path_item_attempt_data[$item_id])
            {
                $learning_path_item_attempt_data[$item_id]['score'] = 0;
                $learning_path_item_attempt_data[$item_id]['time'] = 0;
            }
            
            $learning_path_item_attempt_data[$item_id]['trackers'][] = $tracker;
            $learning_path_item_attempt_data[$item_id]['size'] ++;
            $learning_path_item_attempt_data[$item_id]['score'] += $tracker->get_score();
            
            if ($tracker->get_total_time())
            {
                $learning_path_item_attempt_data[$item_id]['time'] += $tracker->get_total_time();
            }
            
            if ($tracker->get_status() == 'completed' || $tracker->get_status() == 'passed')
            {
                $learning_path_item_attempt_data[$item_id]['completed'] = 1;
            }
            else
            {
                $learning_path_item_attempt_data[$item_id]['active_tracker'] = $tracker;
            }
        }
        
        return $learning_path_item_attempt_data;
    }

    function get_learning_path_tree_menu_url()
    {
        return Path :: get(WEB_PATH) . 'run.php?go=course_viewer&course=' . Request :: get('course') . '&application=weblcms&tool=learning_path&tool_action=test&publication=' . $this->publication->get_id() . '&' . LearningPathDisplay :: PARAM_STEP . '=%s';
    }

    /**
     * @param int $total_steps
     */
    function get_learning_path_previous_url($total_steps)
    {
        return $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), 'step' => $total_steps));
    }

    /**
     * Creates a learning path item tracker
     * 
     * @param LearningPathAttemptTracker $learning_path_tracker
     * @param ComplexContentObjectItem $current_complex_content_object_item
     * @return array LearningPathItemAttemptTracker
     */
    function create_learning_path_item_tracker($learning_path_tracker, $current_complex_content_object_item)
    {
        $parameters = array();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID] = $learning_path_tracker->get_id();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_LP_ITEM_ID] = $current_complex_content_object_item->get_id();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_START_TIME] = time();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_TOTAL_TIME] = 0;
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_SCORE] = 0;
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_MIN_SCORE] = 0;
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_MAX_SCORE] = 0;
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_STATUS] = 'not attempted';
        
        $result = Event :: trigger('attempt_learning_path_item', WeblcmsManager :: APPLICATION_NAME, $parameters);
        return $result[0];
    }

    /**
     * @param int $complex_content_object_id
     */
    function get_learning_path_content_object_item_details_url($complex_content_object_id)
    {
        return $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $this->publication->get_id(), LearningPathDisplay :: PARAM_SHOW_PROGRESS => 'true', ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id, 'attempt_id' => Request :: get('attempt_id')));
    }
    
    /**
     * Get the url of the assessment result
     * 
     * @param int $complex_content_object_id
     * @param unknown_type $details
     */
    function get_learning_path_content_object_assessment_result_url($complex_content_object_id, $details)
    {
        return $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $this->publication->get_id(), LearningPathDisplay :: PARAM_SHOW_PROGRESS => 'true', ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id, LearningPathDisplay :: PARAM_DETAILS => $details));
    }

    /**
     * @return array
     */
    function get_learning_path_attempt_progress_details_reporting_template_name()
    {
        return 'learning_path_attempt_progress_details_reporting_template';
    }

    /**
     * @return array
     */
    function get_learning_path_attempt_progress_reporting_template_name()
    {
        return 'learning_path_attempt_progress_reporting_template';
    }

    function get_learning_path_template_application_name()
    {
        return WeblcmsManager :: APPLICATION_NAME;
    }
    
    /**
     * @param int $document_id
     */
    function get_learning_path_document_preview_url($document_id)
    {
        return $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_DOCUMENT, 'id' => $document_id));
    }
}
?>