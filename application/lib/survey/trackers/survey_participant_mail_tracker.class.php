<?php

//require_once Path :: get_application_path() . 'lib/survey/trackers/survey_question_answer_tracker.class.php';
//require_once Path :: get_repository_path() . 'lib/content_object/survey/context_data_manager/database.class.php';


class SurveyParticipantMailTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    // Can be used for subscribsion of users / classes
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SURVEY_PUBLICATION_ID = 'survey_publication_id';
    const PROPERTY_SEND_DATE = 'send_date';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_SURVEY_PUBLICATION_MAIL_ID = 'survey_publication_mail_id';

    const STATUS_MAIL_SEND = 1;
    const STATUS_MAIL_NOT_SEND = 2;

    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_survey_publication_id($parameters[self :: PROPERTY_SURVEY_PUBLICATION_ID]);
        $this->set_survey_publication_mail_id($parameters[self :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID]);
        $this->set_status($parameters[self :: PROPERTY_STATUS]);
        $this->set_send_date(time());
    }

    /**
     * Inherited
     */
    function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_SURVEY_PUBLICATION_ID, self :: PROPERTY_SEND_DATE, self :: PROPERTY_STATUS, self :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID));
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

    function get_survey_publication_mail_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID);
    }

    function set_survey_publication_mail_id($survey__publication_mail_id)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID, $survey__publication_mail_id);
    }

    function get_send_date()
    {
        return $this->get_default_property(self :: PROPERTY_SEND_DATE);
    }

    function set_send_date($date)
    {
        $this->set_default_property(self :: PROPERTY_SEND_DATE, $date);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>