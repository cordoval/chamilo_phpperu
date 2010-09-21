<?php

//require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
//require_once Path :: get_application_path() . 'lib/survey/trackers/survey_question_answer_tracker.class.php';
//require_once Path :: get_application_path() . 'lib/survey/survey_menu.class.php';
//require_once Path :: get_repository_path() . 'lib/content_object/survey/context_data_manager/context_data_manager.class.php';

class SurveyManagerViewerComponent extends SurveyManager
{
//    private $datamanager;
    
    private $publication_id;
//    private $survey;
//    private $pages;
//    private $questions;
//    private $publication;
//    private $participant_id;
    private $invitee_id;
//    private $user_id;
//    private $active_tracker;
//    private $with_menu;
//    private $trackers;
//    private $tracker_count = 0;
//    private $trail;

    function run()
    {
        
        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        $this->set_parameter(SurveyManager :: PARAM_PUBLICATION_ID, $this->publication_id);
        
        $this->set_parameter(SurveyManager :: PARAM_PARTICIPANT_ID, Request :: get(SurveyManager :: PARAM_PARTICIPANT_ID));
        
        $this->invitee_id = Request :: get(SurveyManager :: PARAM_INVITEE_ID);
        
        if ($this->invitee_id)
        {
            $this->user_id = $this->invitee_id;
            $this->set_parameter(SurveyManager :: PARAM_INVITEE_ID, $this->invitee_id);
        }
        else
        {
            $this->user_id = $this->get_user_id();
        }
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_VIEW, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $this->user_id))
        {
            Display :: not_allowed();
        }
        
        // Retrieving survey
        //        $this->datamanager = SurveyDataManager :: get_instance();
        

        //        if (Request :: get(SurveyManager :: PARAM_PARTICIPANT_ID))
        //        {
        //            $this->participant_id = Request :: get(SurveyManager :: PARAM_PARTICIPANT_ID);
        //            
        //            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $this->participant_id);
        //            $trackers = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1);
        //            $this->active_tracker = $trackers->next_result();
        //            
        //            $this->set_parameter(SurveyManager :: PARAM_PARTICIPANT_ID, $this->participant_id);
        //            $this->set_publication_variables($this->active_tracker->get_survey_publication_id());
        //            
        //            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
        //            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->active_tracker->get_user_id());
        //            $condition = new AndCondition($conditions);
        //            
        //            $this->trackers = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        //        	$this->tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        //            
        //        }
        

        //        else
        //        {
        //            $this->set_publication_variables(Request :: get(SurveyManager :: PARAM_PUBLICATION_ID));
        //            
        //            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
        //            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->user_id);
        //            $condition = new AndCondition($conditions);
        //            
        //            $tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        //            
        //            //            dump($this->user_id);
        ////            dump($tracker_count);
        ////            dump($this->publication);
        //            
        //            if ($tracker_count == 0)
        //            {
        ////                dump('hi');
        //            	if(!$this->publication->create_participant_trackers($this->user_id)){
        //            		$message = 'NoContext';
        //            		$this->redirect(Translation :: get($message),  true, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        //            	}
        //            }
        //            
        //            $this->tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        //            
        //            $this->trackers = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        //            //            
        //            //            if (count($this->trackers) === 0)
        //            //            {
        //            //                $this->not_allowed($trail, false);
        //            //            }
        //            //            
        //            //            else
        //            //            {
        //            $this->active_tracker = $this->trackers->next_result();
        //            $this->set_parameter(SurveyManager :: PARAM_PARTICIPANT_ID, $this->active_tracker->get_id());
        //            //            }
        //        }
        

        //        $this->trail = BreadcrumbTrail :: get_instance();
        //        if ($this->pub->is_test())
        //        {
        //            $this->trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
        //            $this->trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_TESTCASES, TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PARTICIPANTS, TestcaseManager :: PARAM_PUBLICATION_ID => $this->pid)), Translation :: get('BrowseTestCaseSurveyParticipants')));
        //        
        //        }
        //        else
        //        {
        //            $this->trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        //        
        //        }
        //        $this->trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_PUBLICATION_ID => $this->pid)), Translation :: get('TakeSurvey')));
        

        //        if ($this->pub && ! $this->pub->is_visible_for_target_user($this->get_user()))
        //        {
        //            $this->not_allowed($trail, false);
        //        }
        

        //        $db = SurveyContextDataManager :: get_instance();
        //        $context = $db->retrieve_survey_context_by_id($this->active_tracker->get_context_id());
        //        
        //        $this->survey->set_context_instance($context);
        

        ComplexDisplay :: launch(Survey :: get_type_name(), $this, false);
    }

//    function display_header($trail)
//    {
//        //        if ($trail)
//        //        {
//        //            $this->trail->merge($trail);
//        //        }
//        
//
//        parent :: display_header($this->trail);
//        
////        if ($this->tracker_count > 1)
////        {
////            $this->with_menu = true;
////            echo $this->get_menu_html();
////        }
////        
////        if ($this->with_menu)
////        {
////            $width = 80;
////        }
////        else
////        {
////            $width = 100;
////        }
////        echo '<div style="float: right; width: ' . $width . '%;">';
//    }

//    function display_footer()
//    {
//        echo '<div class="clear"></div>';
//        echo '</div>';
//        
//        parent :: display_footer();
//    }

//    function get_trackers()
//    {
//        return $this->trackers;
//    }
//
//    private function set_publication_variables($survey_publication_id)
//    {
//        $this->publication_id = $survey_publication_id;
//        $this->publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_publication_id);
//        $this->survey = $this->publication->get_publication_object();
//        $this->set_parameter(SurveyManager :: PARAM_PUBLICATION_ID, $this->publication_id);
//    }

//    function get_menu_html()
//    {
//        $survey_menu = new SurveyMenu($this->get_participant());
//        $html = array();
//        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
//        $html[] = $survey_menu->render_as_tree();
//        $html[] = '</div>';
//        return implode("\n", $html);
//    }

//    function get_context_template_id()
//    {
//        return $this->active_tracker->get_context_template_id();
//    }
//
//    function get_participant_id()
//    {
//        return $this->active_tracker->get_id();
//    }
//
//    function get_root_content_object()
//    {
//        return $this->survey;
//    }
//
//    function get_participant()
//    {
//        return $this->get_parameter(SurveyManager :: PARAM_PARTICIPANT_ID);
//    }
//
//    function save_answer($complex_question_id, $answer)
//    {
//        
//        $dummy = new SurveyQuestionAnswerTracker();
//        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->active_tracker->get_id());
//        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $complex_question_id);
//        $condition = new AndCondition($conditions);
//        $trackers = $dummy->retrieve_tracker_items($condition);
//        if (count($trackers) === 1)
//        {
//            $trackers[0]->set_answer($answer);
//            $trackers[0]->update();
//        }
//        else
//        {
//            $parameters = array();
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->active_tracker->get_id();
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = $this->active_tracker->get_context_id();
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;
//            
//            Event :: trigger('attempt_question', 'survey', $parameters);
//        }
//        //test for better tracing of setting status of trackers.
//    }
//
//    function get_answer($question)
//    {
//        $dummy = new SurveyQuestionAnswerTracker();
//        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->get_participant_id());
//        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $question->get_id());
//        $condition = new AndCondition($conditions);
//        $trackers = $dummy->retrieve_tracker_items($condition);
//        
//        if (count($trackers) != 0)
//        {
//            return $trackers[0]->get_answer();
//        }
//        else
//        {
//            return null;
//        }
//    }
//
//    function finish_survey($percent)
//    {
//        $tracker = $this->active_tracker;
//        $tracker->set_progress($percent);
//        if ($percent >= 100)
//        {
//            $tracker->set_status(SurveyParticipantTracker :: STATUS_FINISHED);
//        }
//        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
//        $tracker->update();
//        
//        foreach ($trackers as $tracker)
//        {
//            $status = $tracker->get_status();
//            if ($status === SurveyParticipantTracker :: STATUS_NOTSTARTED)
//            {
//                $tracker->set_status(SurveyParticipantTracker :: STATUS_STARTED);
//                $tracker->update();
//            }
//        
//        }
//    
//    }
//
//    function get_current_attempt_id()
//    {
//        return $this->active_tracker->get_id();
//    }
//
//    function get_go_back_url()
//    {
//        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS, SurveyManager :: PARAM_PUBLICATION_ID => null, SurveyManager :: PARAM_PARTICIPANT_ID => null));
//    }
//
//    function parse($value)
//    {
//        
//        $context = $this->survey->get_context_instance();
//        $explode = explode('$V{', $value);
//        
//        $new_value = array();
//        foreach ($explode as $part)
//        {
//            
//            $vars = explode('}', $part);
//            
//            if (count($vars) == 1)
//            {
//                $new_value[] = $vars[0];
//            }
//            else
//            {
//                $var = $vars[0];
//                
//                $replace = $context->get_additional_property($var);
//                
//                $new_value[] = $replace . ' ' . $vars[1];
//            }
//        
//        }
//        return implode(' ', $new_value);
//    }
//
//    //    function get_total_questions(){
//    //    	return $this->survey->count_pages();
//    //    }
//    //    
//    //	function get_total_pages(){
//    //    	return $this->survey->count_pages();
//    //    }
//    
//
//    function get_survey()
//    {
//        return $this->survey;
//    }

}

?>