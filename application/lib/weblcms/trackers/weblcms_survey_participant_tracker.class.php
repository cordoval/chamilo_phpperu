<?php

/**
 * $Id: weblcms_survey_participant_tracker.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.trackers
 */

require_once Path :: get_application_path() . 'lib/survey/trackers/survey_question_answer_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/context_data_manager/database_context_data_manager.class.php';

class WeblcmsSurveyParticipantTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;
    
    // Can be used for subscribsion of users / classes
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SURVEY_PUBLICATION_ID = 'survey_publication_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_PROGRESS = 'progress';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';
    const PROPERTY_CONTEXT_NAME = 'context_name';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const STATUS_STARTED = 'started';
    const STATUS_NOTSTARTED = 'notstarted';
    const STATUS_FINISHED = 'finished';

    /**
     * Constructor sets the default values
     */
    function WeblcmsSurveyParticipantTracker()
    {
        parent :: MainTracker('weblcms_survey_participant_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $user = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_USER_ID];
        $survey = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID];
        $progress = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_PROGRESS];
        $context = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_CONTEXT_ID];
        $context_template = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID];
        $context_name = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_CONTEXT_NAME];
        $status = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_STATUS];
        $parent_id = $parameters[WeblcmsSurveyParticipantTracker :: PROPERTY_PARENT_ID];
        
        $this->set_user_id($user);
        $this->set_survey_publication_id($survey);
        $this->set_start_time(time());
        
        if ($status)
        {
            $this->set_status($status);
        }
        
        $this->set_date(time());
        $this->set_progress($progress);
        $this->set_context_id($context);
        $this->set_context_template_id($context_template);
        $this->set_context_name($context_name);
        $this->set_parent_id($parent_id);
        
        $this->create();
        
        return $this;
    }

    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
        return false;
    }

    /**
     * Inherited
     */
    function get_default_property_names()
    {
        return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_USER_ID, self :: PROPERTY_SURVEY_PUBLICATION_ID, self :: PROPERTY_DATE, self :: PROPERTY_PROGRESS, self :: PROPERTY_STATUS, self :: PROPERTY_PARENT_ID, self :: PROPERTY_START_TIME, self :: PROPERTY_TOTAL_TIME, self :: PROPERTY_CONTEXT_ID, self::PROPERTY_CONTEXT_TEMPLATE_ID,self :: PROPERTY_CONTEXT_NAME));
    }

    function get_user_id()
    {
        return $this->get_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_survey_publication_id()
    {
        return $this->get_property(self :: PROPERTY_SURVEY_PUBLICATION_ID);
    }

    function set_survey_publication_id($survey__publication_id)
    {
        $this->set_property(self :: PROPERTY_SURVEY_PUBLICATION_ID, $survey__publication_id);
    }

    function get_date()
    {
        return $this->get_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_property(self :: PROPERTY_DATE, $date);
    }

    function get_progress()
    {
        return $this->get_property(self :: PROPERTY_PROGRESS);
    }

    function set_progress($progress)
    {
        $this->set_property(self :: PROPERTY_PROGRESS, $progress);
    }

    function get_status()
    {
        return $this->get_property(self :: PROPERTY_STATUS);
    }

    function set_context_id($context_id)
    {
        $this->set_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    function get_context_id()
    {
        return $this->get_property(self :: PROPERTY_CONTEXT_ID);
    }

    function set_context_template_id($context_template_id)
    {
        $this->set_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $context_template_id);
    }

    function get_context_template_id()
    {
        return $this->get_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function set_parent_id($parent_id)
    {
        $this->set_property(self :: PROPERTY_PARENT_ID, $parent_id);
    }

    function get_parent_id()
    {
        return $this->get_property(self :: PROPERTY_PARENT_ID);
    }

    function set_context_name($context_name)
    {
        $this->set_property(self :: PROPERTY_CONTEXT_NAME, $context_name);
    }

    function get_context_name()
    {
        return $this->get_property(self :: PROPERTY_CONTEXT_NAME);
    }

    function set_status($status)
    {
        $this->set_property(self :: PROPERTY_STATUS, $status);
    }

    function get_start_time()
    {
        return $this->get_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time($start_time)
    {
        $this->set_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_total_time()
    {
        return $this->get_property(self :: PROPERTY_TOTAL_TIME);
    }

    function set_total_time($total_time)
    {
        $this->set_property(self :: PROPERTY_TOTAL_TIME, $total_time);
    }

    function empty_tracker($event)
    {
        $this->remove();
    }

    //    function count_participants($publication)
    //    {
    //        $condition = new EqualityCondition(self :: PROPERTY_SURVEY_PUBLICATION_ID, $publication->get_id());
    //        $trackers = $this->retrieve_tracker_items($condition);
    //        return count($trackers);
    //    }
    

    function has_children()
    {
        $condition = new EqualityCondition(self :: PROPERTY_PARENT_ID, $this->get_id());
        return $this->count_tracker_items($condition);
    }

    function is_participant($publication, $user_id)
    {
        $conditions[] = new EqualityCondition(self :: PROPERTY_SURVEY_PUBLICATION_ID, $publication->get_id());
        $conditions[] = new EqualityCondition(self :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(self :: PROPERTY_PROGRESS, 100);
        $condition = new AndCondition($conditions);
        
        $trackers = $this->retrieve_tracker_items($condition);
        if (count($trackers) != 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    
    }

    function delete()
    {
        $succes = parent :: delete();
        
        $condition = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->get_id());
        $dummy = new SurveyQuestionAnswerTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
        {
            $tracker->delete();
        }
        
        //we can't delete context because other trackers may use them !
        //        $dm = SurveyContextDataManager :: get_instance();
        //        $survey_context = $dm->retrieve_survey_context_by_id($this->get_context_id());
        //        if ($survey_context != null)
        //        {
        //          $survey_context->delete();
        //        }
        

        return $succes;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>