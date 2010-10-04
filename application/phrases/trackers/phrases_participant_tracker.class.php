<?php
/**
 * @package application.lib.survey.trackers
 */

class PhrasesParticipantTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_LANGUAGE_ID = 'language_id';
    const PROPERTY_MASTERY_LEVEL_ID = 'mastery_level_id';
    const PROPERTY_PROGRESS = 'progress';
    const PROPERTY_AMOUNT = 'amount';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';

    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[PhrasesParticipantTracker :: PROPERTY_USER_ID]);
        $this->set_language_id($parameters[PhrasesParticipantTracker :: PROPERTY_LANGUAGE_ID]);
        $this->set_mastery_level_id($parameters[PhrasesParticipantTracker :: PROPERTY_MASTERY_LEVEL_ID]);
        $this->set_start_time(time());
        $this->set_progress($parameters[PhrasesParticipantTracker :: PROPERTY_PROGRESS]);
        $this->set_amount($parameters[PhrasesParticipantTracker :: PROPERTY_AMOUNT]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_LANGUAGE_ID, self :: PROPERTY_MASTERY_LEVEL_ID, self :: PROPERTY_PROGRESS, self :: PROPERTY_AMOUNT, self :: PROPERTY_START_TIME, self :: PROPERTY_TOTAL_TIME));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_language_id()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE_ID);
    }

    function set_language_id($language_id)
    {
        $this->set_default_property(self :: PROPERTY_LANGUAGE_ID, $language_id);
    }

    function get_mastery_level_id()
    {
        return $this->get_default_property(self :: PROPERTY_MASTERY_LEVEL_ID);
    }

    function set_mastery_level_id($mastery_level_id)
    {
        $this->set_default_property(self :: PROPERTY_MASTERY_LEVEL_ID, $mastery_level_id);
    }

    function get_progress()
    {
        return $this->get_default_property(self :: PROPERTY_PROGRESS);
    }

    function set_progress($progress)
    {
        $this->set_default_property(self :: PROPERTY_PROGRESS, $progress);
    }

    function get_amount()
    {
        return $this->get_default_property(self :: PROPERTY_AMOUNT);
    }

    function set_amount($amount)
    {
        $this->set_default_property(self :: PROPERTY_AMOUNT, $amount);
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

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>