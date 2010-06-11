<?php

/**
 * $Id: phrases_participant_tracker.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.trackers
 */

//require_once Path :: get_application_path() . 'lib/survey/trackers/survey_question_answer_tracker.class.php';
//require_once Path :: get_repository_path() . 'lib/content_object/survey/context_data_manager/database_context_data_manager.class.php';

class PhrasesParticipantTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;

    // Can be used for subscribsion of users / classes
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_LANGUAGE_ID = 'language_id';
    const PROPERTY_MASTERY_LEVEL_ID = 'mastery_level_id';
    const PROPERTY_PROGRESS = 'progress';
    const PROPERTY_AMOUNT = 'amount';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';

    /**
     * Constructor sets the default values
     */
    function PhrasesParticipantTracker()
    {
        parent :: MainTracker('phrases_participant_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $user = $parameters[PhrasesParticipantTracker :: PROPERTY_USER_ID];
        $language_id = $parameters[PhrasesParticipantTracker :: PROPERTY_LANGUAGE_ID];
        $mastery_level_id = $parameters[PhrasesParticipantTracker :: PROPERTY_MASTERY_LEVEL_ID];
        $progress = $parameters[PhrasesParticipantTracker :: PROPERTY_PROGRESS];
        $amount = $parameters[PhrasesParticipantTracker :: PROPERTY_AMOUNT];

        $this->set_user_id($user);
        $this->set_language_id($language_id);
        $this->set_mastery_level_id($mastery_level_id);
        $this->set_start_time(time());
        $this->set_progress($progress);
        $this->set_amount($amount);

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
        return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_USER_ID, self :: PROPERTY_LANGUAGE_ID, self :: PROPERTY_MASTERY_LEVEL_ID, self :: PROPERTY_PROGRESS, self :: PROPERTY_AMOUNT, self :: PROPERTY_START_TIME, self :: PROPERTY_TOTAL_TIME));
    }

    function get_user_id()
    {
        return $this->get_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_language_id()
    {
        return $this->get_property(self :: PROPERTY_LANGUAGE_ID);
    }

    function set_language_id($language_id)
    {
        $this->set_property(self :: PROPERTY_LANGUAGE_ID, $language_id);
    }

    function get_mastery_level_id()
    {
        return $this->get_property(self :: PROPERTY_MASTERY_LEVEL_ID);
    }

    function set_mastery_level_id($mastery_level_id)
    {
        $this->set_property(self :: PROPERTY_MASTERY_LEVEL_ID, $mastery_level_id);
    }

    function get_progress()
    {
        return $this->get_property(self :: PROPERTY_PROGRESS);
    }

    function set_progress($progress)
    {
        $this->set_property(self :: PROPERTY_PROGRESS, $progress);
    }

    function get_amount()
    {
        return $this->get_property(self :: PROPERTY_AMOUNT);
    }

    function set_amount($amount)
    {
        $this->set_property(self :: PROPERTY_AMOUNT, $amount);
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

//    function delete()
//    {
//        $succes = parent :: delete();
//
//        $condition = new EqualityCondition(PhrasesQuestionAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
//        $dummy = new PhrasesQuestionAnswerTracker();
//        $trackers = $dummy->retrieve_tracker_items($condition);
//        foreach ($trackers as $tracker)
//        {
//            $tracker->delete();
//        }
//
//        return $succes;
//    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>