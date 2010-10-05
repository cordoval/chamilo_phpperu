<?php
/**
 * @package application.lib.survey.trackers
 */

require_once Path :: get_application_path() . 'lib/survey/trackers/survey_question_answer_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/context_data_manager/database_context_data_manager.class.php';

class SurveyParticipantTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;
	
    const CREATE_PARTICIPANT_EVENT = 'create_survey_participant';
    
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

    function validate_parameters(array $parameters = array())
    {
        $status = $parameters[SurveyParticipantTracker :: PROPERTY_STATUS];

        $this->set_user_id($parameters[SurveyParticipantTracker :: PROPERTY_USER_ID]);
        $this->set_survey_publication_id($parameters[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID]);
        $this->set_start_time(time());

        if ($status)
        {
            $this->set_status($status);
        }

        $this->set_date(time());
        $this->set_progress($parameters[SurveyParticipantTracker :: PROPERTY_PROGRESS]);
        $this->set_context_id($parameters[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID]);
        $this->set_context_template_id($parameters[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID]);
        $this->set_context_name($parameters[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME]);
        $this->set_parent_id($parameters[SurveyParticipantTracker :: PROPERTY_PARENT_ID]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_USER_ID, self :: PROPERTY_SURVEY_PUBLICATION_ID, self :: PROPERTY_DATE, self :: PROPERTY_PROGRESS, self :: PROPERTY_STATUS, self :: PROPERTY_PARENT_ID, self :: PROPERTY_START_TIME,
                self :: PROPERTY_TOTAL_TIME, self :: PROPERTY_CONTEXT_ID, self :: PROPERTY_CONTEXT_TEMPLATE_ID, self :: PROPERTY_CONTEXT_NAME));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_survey_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_PUBLICATION_ID);
    }

    function set_survey_publication_id($survey__publication_id)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_PUBLICATION_ID, $survey__publication_id);
    }

    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }

    function get_progress()
    {
        return $this->get_default_property(self :: PROPERTY_PROGRESS);
    }

    function set_progress($progress)
    {
        $this->set_default_property(self :: PROPERTY_PROGRESS, $progress);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_context_id($context_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    function get_context_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_ID);
    }

    function set_context_template_id($context_template_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $context_template_id);
    }

    function get_context_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function set_parent_id($parent_id)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
    }

    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    function set_context_name($context_name)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_NAME, $context_name);
    }

    function get_context_name()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_NAME);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_total_time()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
    }

    function set_total_time($total_time)
    {
        $this->set_default_property(self :: PROPERTY_TOTAL_TIME, $total_time);
    }

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