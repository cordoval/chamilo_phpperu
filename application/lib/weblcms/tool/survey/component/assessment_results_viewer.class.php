<?php
/**
 * $Id: assessment_results_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/assessment_results_table_admin/assessment_results_table_overview.class.php';
require_once dirname(__FILE__) . '/assessment_results_table_admin/assessment_results_table_detail.class.php';
require_once dirname(__FILE__) . '/assessment_results_table_student/assessment_results_table_overview.class.php';
require_once dirname(__FILE__) . '/../../../browser/content_object_publication_category_tree.class.php';
require_once dirname(__FILE__) . '/assessment_tester.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_question_attempts_tracker.class.php';

class AssessmentToolResultsViewerComponent extends AssessmentToolComponent
{
    private $object;

    function run()
    {
        if (Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT))
        {
            $this->view_single_result();
        }
        else 
            if (Request :: get(AssessmentTool :: PARAM_ASSESSMENT))
            {
                $this->view_assessment_results();
            }
            else
            {
                $this->view_all_results();
            }
    
    }

    function view_all_results()
    {
        $crumbs[] = new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), Translation :: get('ViewResults'));
        
        $visible = $this->display_header();
        if (! $visible)
        {
            return;
        }
        
        $tree_id = WeblcmsManager :: PARAM_CATEGORY;
        $params = array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS);
        $tree = new ContentObjectPublicationCategoryTree($this, $tree_id, $params);
        $this->set_parameter($tree_id, Request :: get($tree_id));
        echo '<div style="width:18%; float: left; overflow: auto;">';
        echo $tree->as_html();
        echo '</div>';
        echo '<div style="width:80%; padding-left: 1%; float:right; ">';
        
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $table = new AssessmentResultsTableOverviewAdmin($this, $this->get_user());
        }
        else
        {
            $table = new AssessmentResultsTableOverviewStudent($this, $this->get_user());
        }
        
        echo $table->as_html();
        echo '</div>';
        $this->display_footer();
    }

    function view_assessment_results()
    {
        $pid = Request :: get(AssessmentTool :: PARAM_ASSESSMENT);
        $crumbs[] = new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), Translation :: get('ViewResults'));
        $crumbs[] = new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $pid)), Translation :: get('AssessmentResults'));
        
        $visible = $this->display_header();
        if (! $visible)
        {
            return;
        }
        
        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($pid);
        $assessment = $publication->get_content_object();
        
        echo '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/assessment.png);">';
        echo '<div class="title">';
        echo $assessment->get_title();
        echo '</div>';
        echo $assessment->get_description();
        echo '<div class="title">';
        echo Translation :: get('Statistics');
        echo '</div>';
        $track = new WeblcmsAssessmentAttemptsTracker();
        
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $usr = $this->get_user_id();
        }
        
        $avg = $track->get_average_score($publication, $usr);
        if (! isset($avg))
        {
            $avg_line = 'No results';
        }
        else
        {
            $avg_line = $avg . '%';
        }
        echo Translation :: get('AverageScore') . ': ' . $avg_line;
        echo '<br/>' . Translation :: get('TimesTaken') . ': ' . $track->get_times_taken($publication, $usr);
        echo '</div>';
        $table = new AssessmentResultsTableDetail($this, $this->get_user(), Request :: get(AssessmentTool :: PARAM_ASSESSMENT));
        echo $table->as_html();
        
        $this->display_footer();
    }
    
    private $user_assessment;
    private $trail;

    function view_single_result()
    {
        $uaid = Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT);
        
        $track = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
        $user_assessments = $track->retrieve_tracker_items($condition);
        $this->user_assessment = $user_assessments[0];
        
        $this->trail = $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), Translation :: get('ViewResults')));
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $this->user_assessment->get_assessment_id())), Translation :: get('AssessmentResults')));
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $uaid)), Translation :: get('Details')));
        
        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($this->user_assessment->get_assessment_id());
        $object = $publication->get_content_object();
        
        $_GET['display_action'] = 'view_result';
        
        $this->set_parameter('uaid', $uaid);
        
        $this->object = $object;
        ComplexDisplay :: launch($object->get_type(), $this);
    }

    /*function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}

    	return parent :: display_header();
    }*/
    
    function retrieve_assessment_results()
    {
        $condition = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->user_assessment->get_id());
        
        $dummy = new WeblcmsQuestionAttemptsTracker();
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
        $conditions[] = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->user_assessment->get_id());
        $conditions[] = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $question_cid);
        $condition = new AndCondition($conditions);
        
        $dummy = new WeblcmsQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $tracker = $trackers[0];
        $tracker->set_score($score);
        $tracker->set_feedback($feedback);
        $tracker->update();
    }

    function change_total_score($total_score)
    {
        $this->user_assessment->set_total_score($total_score);
        $this->user_assessment->update();
    }

    function can_change_answer_data()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);
    }

    function display_header($breadcrumbs = array())
    {
        if (! Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
        {
            if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
            {
                Display :: not_allowed();
                return false;
            }
        }
        $trail = BreadcrumbTrail :: get_instance();
        foreach ($breadcrumbs as $breadcrumb)
        {
            $trail->add($breadcrumb);
        }
        parent :: display_header();
        
        $this->action_bar = $this->get_toolbar();
        if ($this->action_bar)
            echo $this->action_bar->as_html();
        
        return true;
    }

    function get_toolbar()
    {
        if (Request :: get(AssessmentTool :: PARAM_ASSESSMENT) && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            
            $aid = Request :: get(AssessmentTool :: PARAM_ASSESSMENT);
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('DownloadDocuments'), Theme :: get_common_image_path() . 'action_save.png', $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_SAVE_DOCUMENTS, AssessmentTool :: PARAM_ASSESSMENT => $aid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('DeleteAllResults'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_DELETE_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $aid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            return $action_bar;
        }
    
    }

    function get_root_content_object()
    {
        return $this->object;
    }
}
?>