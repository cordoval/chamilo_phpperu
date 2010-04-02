<?php
/**
 * $Id: learning_path_statistics_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
require_once dirname(__FILE__) . '/../../../trackers/weblcms_lp_attempt_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_lpi_attempt_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_lpi_attempt_objective_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_learning_path_question_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_tree.class.php';

class LearningPathToolStatisticsViewerComponent extends LearningPathToolComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses learnpath tool');
        
        $pid = Request :: get('pid');
        
        if (! $pid)
        {
            $this->display_header($trail, true);
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
        }
        
        $stats_action = Request :: get('stats_action');
        switch ($stats_action)
        {
            case 'delete_lp_attempt' :
                $this->delete_lp_attempt(Request :: get('attempt_id'));
                exit();
            case 'delete_lpi_attempts' :
                $this->delete_lpi_attempts_from_item(Request :: get('item_id'));
                exit();
            case 'delete_lpi_attempt' :
                $this->delete_lpi_attempt(Request :: get('delete_id'));
                exit();
        }
        
        $dm = WeblcmsDataManager :: get_instance();
        $publication = $dm->retrieve_content_object_publication($pid);
        $root_object = $publication->get_content_object();
        
        $parameters = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => $pid);
        $url = $this->get_url($parameters);
        
        $trail->add(new Breadcrumb($url, Translation :: get('Statistics') . ' ' . Translation :: get('of') . ' ' . $root_object->get_title()));
        
        $attempt_id = Request :: get('attempt_id');
        
        if ($attempt_id)
        {
            $tracker = $this->retrieve_tracker($attempt_id);
            $attempt_data = $this->retrieve_tracker_items($tracker);
            $menu = $this->get_menu($root_object->get_id(), null, $pid, $attempt_data);
            
            $parameters['attempt_id'] = $attempt_id;
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
                
                $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('AssessmentResult')));
                $this->set_parameter('tool_action', 'stats');
                $this->set_parameter('pid', $pid);
                $this->set_parameter('attempt_id', $attempt_id);
                $this->set_parameter('cid', $cid);
                $this->set_parameter('details', $details);
                $_GET['display_action'] = 'view_result';
                
                $object = $objects[$cid];
                
                $display = ComplexDisplay :: factory($this, $object->get_type());
                $display->set_root_lo($object);
            }
            else
            {
                require_once (Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_progress_reporting_template.class.php');
                $parameters = array('objects' => $menu->get_objects(), 'attempt_data' => $attempt_data, 'cid' => $cid, 'url' => $url, 'delete' => true);
                $template = new LearningPathProgressReportingTemplate($this, 0, $parameters, $trail, $objects[$cid]);
                $display = $template->to_html();
            }
        }
        else
        {
            require_once (Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_attempts_reporting_template.class.php');
            $parameters = array('publication' => $publication, 'course' => $this->get_course_id(), 'url' => $url);
            $template = new LearningPathAttemptsReportingTemplate($this);
            $display = $template->to_html();
        }
        
        $this->display_header($trail, true);
        
        if (get_class($display) == 'AssessmentDisplay')
        {
            $display->run();
        }
        else
        {
            echo $display;
        }
        
        $this->display_footer();
    }

    private function get_menu($root_object_id, $selected_object_id, $pid, $lpi_tracker_data)
    {
        $menu = new LearningPathTree($root_object_id, $selected_object_id, '?go=courseviewer&course=' . Request :: get('course') . '&application=weblcms&tool=learning_path&tool_action=view&pid=' . $pid . '&' . LearningPathTool :: PARAM_LP_STEP . '=%s', $lpi_tracker_data);
        
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
        
        $params = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'));
        
        $this->redirect(Translation :: get('LpAttemptDeleted'), false, $params, array());
    }

    private function delete_lpi_attempt($lpi_attempt_id)
    {
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $lpi_attempt_id);
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
            $tracker->delete();
        
        $params = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'attempt_id' => Request :: get('attempt_id'), 'cid' => Request :: get('cid'));
        
        $this->redirect(Translation :: get('LpiAttemptDeleted'), false, $params, array());
    }

    private function delete_lpi_attempts_from_item($item_id)
    {
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_ITEM_ID, $item_id);
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
            $tracker->delete();
        
        $params = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'attempt_id' => Request :: get('attempt_id'));
        
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

}
?>