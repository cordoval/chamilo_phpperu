<?php

require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_question_answer_tracker.class.php';
require_once Path :: get_application_path() . 'lib/survey/survey_menu.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/context/survey_student_context/survey_student_context.class.php';

class SurveyManagerViewerComponent extends SurveyManagerComponent
{
    private $datamanager;
    
    private $pub;
    private $survey;
    private $pid;
    private $participant_id;
    private $active_tracker;
    private $with_menu;
    private $trackers;

    function run()
    {
        
        // Retrieving survey
        $this->datamanager = SurveyDataManager :: get_instance();
        
        if (Request :: get(SurveyManager :: PARAM_SURVEY_PARTICIPANT))
        {
            $this->participant_id = Request :: get(SurveyManager :: PARAM_SURVEY_PARTICIPANT);
            $track = new SurveyParticipantTracker();
            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $this->participant_id);
            $trackers = $track->retrieve_tracker_items($condition);
            $this->active_tracker = $trackers[0];
            
            $this->set_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT, $this->participant_id);
            $this->set_publication_variables($this->active_tracker->get_survey_publication_id());
            
            $track = new SurveyParticipantTracker();
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->active_tracker->get_user_id());
            $condition = new AndCondition($conditions);
            $this->trackers = $track->retrieve_tracker_items($condition);
        
        }
        else
        {
            if (Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION))
            {
                
                $this->set_publication_variables(Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION));
                
                $track = new SurveyParticipantTracker();
                $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);
                $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->get_user_id());
                $condition = new AndCondition($conditions);
                $this->trackers = $track->retrieve_tracker_items($condition);
                
                if (count($this->trackers) === 0)
                {
                    $this->not_allowed($trail, false);
                }
                
                else
                {
                    $this->active_tracker = $this->trackers[0];
                    $this->set_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT, $this->active_tracker->get_id());
                }
            }
        }
        
        $trail = new BreadcrumbTrail();
        if ($this->pub->is_test())
        {
            $trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_TESTCASE, TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PARTICIPANTS, TestcaseManager :: PARAM_SURVEY_PUBLICATION => $this->pid)), Translation :: get('BrowseTestCaseSurveyParticipants')));
        
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        
        }
        $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $this->pid)), Translation :: get('TakeSurvey')));
        
        if ($this->pub && ! $this->pub->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed($trail, false);
        }
        
        $this->display_header($trail);
        
        $db = SurveyContextDataManager :: get_instance();
        $context = $db->retrieve_survey_context_by_id($this->active_tracker->get_context_id());
        $this->survey->set_context_instance($context);
        
        if (count($this->trackers) > 1)
        {
            $this->with_menu = true;
            echo $this->get_menu_html();
        }
        
        echo $this->get_survey_html();
        $this->display_footer();
    }

    private function set_publication_variables($survey_publication_id)
    {
        $this->pid = $survey_publication_id;
        $this->pub = $this->datamanager->retrieve_survey_publication($this->pid);
        $survey_id = $this->pub->get_content_object();
        $this->survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);
        $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $this->pid);
    }

    function get_menu_html()
    {
        $survey_menu = new SurveyMenu($this->get_participant());
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $survey_menu->render_as_tree();
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_survey_html()
    {
        if ($this->with_menu)
        {
            $width = 80;
        }
        else
        {
            $width = 100;
        }
        $html = array();
        $html[] = '<div style="float: right; width: ' . $width . '%;">';
        $display = ComplexDisplay :: factory($this, $this->survey->get_type());
        $display->set_root_lo($this->survey);
        $html[] = $display->run();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_participant()
    {
        return $this->get_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT);
    }

    function save_answer($complex_question_id, $answer)
    {
        
        $dummy = new SurveyQuestionAnswerTracker();
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->active_tracker->get_id());
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $complex_question_id);
        $condition = new AndCondition($conditions);
        $trackers = $dummy->retrieve_tracker_items($condition);
        if (count($trackers) === 1)
        {
            $trackers[0]->set_answer($answer);
            $trackers[0]->update();
        }
        else
        {
            $parameters = array();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->active_tracker->get_id();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = $this->active_tracker->get_context_id();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;
            
            Events :: trigger_event('attempt_question', 'survey', $parameters);
        }
    }

    function finish_survey($percent)
    {
        $tracker = $this->active_tracker;
        $tracker->set_progress($percent);
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->update();
        
        $track = new SurveyParticipantTracker();
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->active_tracker->get_user_id());
        $condition = new AndCondition($conditions);
        $trackers = $track->retrieve_tracker_items($condition);
        
        if ($percent === 100)
        {
            $all_finished = false;
            $progress = array();
            
            foreach ($trackers as $tracker)
            {
                $progress[] = $tracker->get_progress();
            }
            
            $finshed = array_intersect($progress, array(100));
            $all_finished = count($progress) == count($finshed);
            if ($all_finished)
            {
                foreach ($trackers as $tracker)
                {
                    $tracker->set_status(SurveyParticipantTracker :: STATUS_FINISHED);
                    $tracker->update();
                }
            }
        
        }
        
        foreach ($trackers as $tracker)
        {
            $status = $tracker->get_status();
            if ($status === SurveyParticipantTracker :: STATUS_NOTSTARTED)
            {
                $tracker->set_status(SurveyParticipantTracker :: STATUS_STARTED);
                $tracker->update();
            }
        
        }
    
    }

    function get_current_attempt_id()
    {
        return $this->active_tracker->get_id();
    }

    function get_go_back_url()
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS, SurveyManager :: PARAM_SURVEY_PUBLICATION => null, SurveyManager :: PARAM_SURVEY_PARTICIPANT => null));
    }

    function parse($value)
    {
        
        $context = $this->survey->get_context_instance();
        $explode = explode('$V{', $value);
        
        $new_value = array();
        foreach ($explode as $part)
        {
            
            $vars = explode('}', $part);
            
            if (count($vars) == 1)
            {
                $new_value[] = $vars[0];
            }
            else
            {
                $var = $vars[0];
                
                $replace = $context->get_additional_property($var);
                
                $new_value[] = $replace . ' ' . $vars[1];
            }
        
        }
        return implode(' ', $new_value);
    }
}

?>