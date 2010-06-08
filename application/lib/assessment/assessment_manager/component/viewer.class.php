<?php
/**
 * $Id: viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */

require_once Path :: get_application_path() . 'lib/assessment/trackers/assessment_assessment_attempts_tracker.class.php';

class AssessmentManagerViewerComponent extends AssessmentManager
{
    private $datamanager;
    
    private $pub;
    private $assessment;
    private $pid;
    private $active_tracker;
    private $trail;

    function run()
    {
        // Retrieving assessment
        $this->datamanager = AssessmentDataManager :: get_instance();
        if (Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION))
        {
            $this->pid = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
            $this->pub = $this->datamanager->retrieve_assessment_publication($this->pid);
            $assessment_id = $this->pub->get_content_object();
            $this->assessment = RepositoryDataManager :: get_instance()->retrieve_content_object($assessment_id);
            $this->set_parameter(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION, $this->pid);
        }
        
        if (Request :: get(AssessmentManager :: PARAM_INVITATION_ID))
        {
            $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, Request :: get(AssessmentManager :: PARAM_INVITATION_ID));
            $invitation = $this->datamanager->retrieve_survey_invitations($condition)->next_result();
            
            $this->pid = $invitation->get_survey_id();
            $this->pub = $this->datamanager->retrieve_assessment_publication($this->pid);
            $assessment_id = $this->pub->get_content_object();
            $this->assessment = RepositoryDataManager :: get_instance()->retrieve_content_object($assessment_id);
            $this->set_parameter(AssessmentManager :: PARAM_INVITATION_ID, Request :: get(AssessmentManager :: PARAM_INVITATION_ID));
        }
        
        if ($this->pub && ! $this->pub->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed($trail, false);
        }
        
        // Checking statistics
        

        $track = new AssessmentAssessmentAttemptsTracker();
        $conditions[] = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $this->pid);
        $conditions[] = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        $trackers = $track->retrieve_tracker_items($condition);
        
        $count = count($trackers);
        
        foreach ($trackers as $tracker)
        {
            if ($tracker->get_status() == 'not attempted')
            {
                $this->active_tracker = $tracker;
                $count --;
                break;
            }
        }
        
        if ($this->assessment->get_maximum_attempts() != 0 && $count >= $this->assessment->get_maximum_attempts())
        {
            Display :: not_allowed();
            return;
        }
        
        if (! $this->active_tracker)
        {
            $this->active_tracker = $this->create_tracker();
        }
        
        $this->trail = $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $this->pid)), Translation :: get('TakeAssessment')));
        
        // Executing assessment
        

        if ($this->assessment->get_assessment_type() == Hotpotatoes :: TYPE_HOTPOTATOES)
        {
            $this->display_header($trail);
            
            $path = $this->assessment->add_javascript(Path :: get(WEB_PATH) . 'application/lib/assessment/ajax/hotpotatoes_save_score.php', $this->get_browse_assessment_publications_url(), $this->active_tracker->get_id());
            echo '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
            //require_once $path;
            $this->display_footer();
            exit();
        }
        else
        {
            $display = ComplexDisplay :: factory($this, $this->assessment->get_type());
            
            //$this->display_header($trail);
            $display->run();
            //$this->display_footer();
        }
    
    }
    
	function get_root_content_object()
    {
    	return $this->assessment;
    }

    function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}
    	
    	parent :: display_header($this->trail);
    }
    
    function create_tracker()
    {
        $args = array('assessment_id' => $this->pid, 'user_id' => $this->get_user_id(), 'total_score' => 0);
        
        $tracker = Events :: trigger_event('attempt_assessment', AssessmentManager :: APPLICATION_NAME, $args);
        
        return $tracker[0];
    }


    function save_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters['assessment_attempt_id'] = $this->active_tracker->get_id();
        $parameters['question_cid'] = $complex_question_id;
        $parameters['answer'] = $answer;
        $parameters['score'] = $score;
        $parameters['feedback'] = '';
        
        Events :: trigger_event('attempt_question', AssessmentManager :: APPLICATION_NAME, $parameters);
    }

    function finish_assessment($total_score)
    {
        $tracker = $this->active_tracker;
        
        $tracker->set_total_score($total_score);
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->set_status('completed');
        $tracker->update();
    }

    function get_current_attempt_id()
    {
        return $this->active_tracker->get_id();
    }

    function get_go_back_url()
    {
        return $this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => null));
    }
}
?>