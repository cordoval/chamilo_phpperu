<?php
namespace application\weblcms\tool\learning_path;

use repository\ComplexDisplay;

use application\weblcms\WeblcmsLearningPathQuestionAttemptsTracker;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsManager;
use application\weblcms\Tool;
use application\weblcms\WeblcmsLpAttemptTracker;
use application\weblcms\WeblcmsLpiAttemptTracker;

use common\extensions\reporting_viewer\ReportingViewer;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\DelegateComponent;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: learning_path_statistics_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_tree.class.php';

class LearningPathToolStatisticsViewerComponent extends LearningPathTool implements DelegateComponent
{
    const PARAM_STAT = 'stats_action';
    const ACTION_DELETE_LP_ATTEMPT = 'delete_lp_attempt';
    const ACTION_DELETE_LPI_ATTEMPT = 'delete_lpi_attempt';
    const ACTION_DELETE_LPI_ATTEMPTS = 'delete_lpi_attempts';
    const PARAM_ITEM_ID = 'item_id';
    const PARAM_DELETE_ID = 'delete_id';

    private $root_content_object;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);

        if (! $pid)
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectSelected', null , Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
        }

        $stats_action = Request :: get(self :: PARAM_STAT);
        switch ($stats_action)
        {
            case self :: ACTION_DELETE_LP_ATTEMPT :
                $this->delete_lp_attempt(Request :: get(LearningPathTool :: PARAM_ATTEMPT_ID));
                exit();
            case self :: ACTION_DELETE_LPI_ATTEMPTS :
                $this->delete_lpi_attempts_from_item(Request :: get('item_id'));
                exit();
            case self :: ACTION_DELETE_LPI_ATTEMPT :
                $this->delete_lpi_attempt(Request :: get('delete_id'));
                exit();
        }

        $dm = WeblcmsDataManager :: get_instance();
        $publication = $dm->retrieve_content_object_publication($pid);
        $root_object = $publication->get_content_object();

        $parameters = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => $pid);
        $url = $this->get_url($parameters);

        $attempt_id = Request :: get(LearningPathTool :: PARAM_ATTEMPT_ID);

        if ($attempt_id)
        {
            $tracker = $this->retrieve_tracker($attempt_id);
            $attempt_data = $this->retrieve_tracker_items($tracker);
            $menu = $this->get_menu($root_object->get_id(), null, $pid, $attempt_data);

            $parameters[LearningPathTool :: PARAM_ATTEMPT_ID] = $attempt_id;
            $url = $this->get_url($parameters);
            $trail->add(new Breadcrumb($url, Translation :: get('AttemptDetails')));

            $cid = Request :: get('cid');
            if ($cid)
            {
                $parameters['cid'] = $cid;
                $url = $this->get_url($parameters);
                $trail->add(new Breadcrumb($url, Translation :: get('ItemDetails')));
            }

            $objects = $menu->get_objects();
            $details = Request :: get('details');

            if ($details)
            {
                $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('AssessmentResult', null , 'application/assessment')));
                $this->set_parameter('tool_action', 'stats');
                $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);
                $this->set_parameter(LearningPathTool :: PARAM_ATTEMPT_ID, $attempt_id);
                $this->set_parameter('cid', $cid);
                $this->set_parameter('details', $details);
                $_GET['display_action'] = 'view_result';

                $object = $objects[$cid];

                $this->root_content_object = $object;
                ComplexDisplay :: launch($object->get_type(), $this);
            }
            else
            {
                if ($cid)
                {
                    /* require_once (Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_attempt_progress_details_reporting_template.class.php');
                      $template = new LearningPathAttemptProgressDetailsReportingTemplate($this);
                      $display = $template->to_html(); */
                    $rtv = ReportingViewer :: construct($this);
                    $rtv->add_template_by_name('learning_path_attempt_progress_details_reporting_template', WeblcmsManager :: APPLICATION_NAME);
                    $rtv->set_breadcrumb_trail($trail);

                    $rtv->run();
                    exit();
                }
                else
                {
                    /* require_once (Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_attempt_progress_reporting_template.class.php');
                      $template = new LearningPathAttemptProgressReportingTemplate($this);
                      $display = $template->to_html(); */
                    $rtv = ReportingViewer :: construct($this);
                    $rtv->add_template_by_name('learning_path_attempt_progress_reporting_template', WeblcmsManager :: APPLICATION_NAME);
                    $rtv->set_breadcrumb_trail($trail);

                    $rtv->run();
                    exit();
                }
            }
        }
        else
        {
            /* require_once (Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_attempts_reporting_template.class.php');
              $parameters = array('publication' => $publication, 'course' => $this->get_course_id(), 'url' => $url);
              $template = new LearningPathAttemptsReportingTemplate($this);
              $display = $template->to_html(); */

            $rtv = ReportingViewer :: construct($this);
            $rtv->add_template_by_name('learning_path_attempts_reporting_template', WeblcmsManager :: APPLICATION_NAME);
            $rtv->set_breadcrumb_trail($trail);

            $rtv->run();
            exit();
        }

        if ($display instanceof AssessmentDisplay)
        {
            $display->run();
        }
        else
        {
            $this->display_header();
            echo $display;
            $this->display_footer();
        }
    }

    private function get_menu($root_object_id, $selected_object_id, $pid, $lpi_tracker_data)
    {
        $menu = new LearningPathTree($root_object_id, $selected_object_id, '?go=courseviewer&course=' . Request :: get('course') . '&application=weblcms&tool=learning_path&tool_action=view&publication=' . $pid . '&' . LearningPathTool :: PARAM_LP_STEP . '=%s', $lpi_tracker_data);

        return $menu;
    }

    // Statistics
    private function retrieve_tracker($attempt_id)
    {
        $condition = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_ID, $attempt_id);
        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        return $trackers[0];
    }

    private function retrieve_tracker_items($lp_tracker)
    {
        $lpi_attempt_data = array();

        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $lp_tracker->get_id());

        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $item_id = $tracker->get_lp_item_id();
            if (! $lpi_attempt_data[$item_id])
            {
                $lpi_attempt_data[$item_id]['score'] = 0;
                $lpi_attempt_data[$item_id]['time'] = 0;
            }

            $lpi_attempt_data[$item_id]['trackers'][] = $tracker;
            $lpi_attempt_data[$item_id]['size'] ++;
            $lpi_attempt_data[$item_id]['score'] += $tracker->get_score();
            if ($tracker->get_total_time())
                $lpi_attempt_data[$item_id]['time'] += $tracker->get_total_time();

            if ($tracker->get_status() == 'completed')
                $lpi_attempt_data[$item_id]['completed'] = 1;
            else
                $lpi_attempt_data[$item_id]['active_tracker'] = $tracker;
        }
        //dump($lpi_attempt_data);
        return $lpi_attempt_data;
    }

    // Deleter functions
    private function delete_lp_attempt($lp_attempt_id)
    {
        $condition = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_ID, $lp_attempt_id);
        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
            $tracker->delete();

        $params = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID));

        $this->redirect(Translation :: get('LpAttemptDeleted'), false, $params, array());
    }

    private function delete_lpi_attempt($lpi_attempt_id)
    {
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $lpi_attempt_id);
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
            $tracker->delete();

        $params = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), LearningPathTool :: PARAM_ATTEMPT_ID => Request :: get(LearningPathTool :: PARAM_ATTEMPT_ID), 'cid' => Request :: get('cid'));

        $this->redirect(Translation :: get('LpiAttemptDeleted'), false, $params, array());
    }

    private function delete_lpi_attempts_from_item($item_id)
    {
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_ITEM_ID, $item_id);
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
            $tracker->delete();

        $params = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), LearningPathTool :: PARAM_ATTEMPT_ID => Request :: get(LearningPathTool :: PARAM_ATTEMPT_ID));

        $this->redirect(Translation :: get('LpiAttemptsDeleted'), false, $params, array());
    }

    function can_change_answer_data()
    {
        return true;
    }

    function retrieve_assessment_results()
    {
        $condition = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, Request :: get('details'));

        $dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        $results = array();

        foreach ($trackers as $tracker)
        {
            $results[$tracker->get_question_cid()] = array('answer' => $tracker->get_answer(), 'feedback' => $tracker->get_feedback(), 'score' => $tracker->get_score());
        }

        return $results;
    }

    function change_answer_data($question_cid, $score, $feedback)
    {
        $conditions[] = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, Request :: get('details'));
        $conditions[] = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $question_cid);
        $condition = new AndCondition($conditions);

        $dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $tracker = $trackers[0];
        $tracker->set_score($score);
        $tracker->set_feedback($feedback);
        $tracker->update();
    }

    function change_total_score($total_score)
    {
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, Request :: get('details'));

        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $lpi_tracker = $trackers[0];

        $lpi_tracker->set_score($total_score);
        $lpi_tracker->update();
    }

    function get_root_content_object()
    {
        return $this->root_content_object;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_learning_path');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('AnnouncementToolBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('AnnouncementToolViewerComponent')));

    //$breadcrumbtrail->add(new Breadcrumb($url, Translation :: get('Statistics') . ' ' . Translation :: get('of') . ' ' . $root_object->get_title()));
    }

    function get_additional_parameters()
    {
        return array();
    }

}

?>