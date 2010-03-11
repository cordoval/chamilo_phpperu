<?php
/**
 * $Id: survey_invitation.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey
 */

class SurveyInvitation extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_INVITATION_CODE = 'invitation_code';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_VALID = 'valid';
    
    const TABLE_NAME = 'survey_invitation';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_SURVEY_ID, self :: PROPERTY_INVITATION_CODE, self :: PROPERTY_EMAIL, self :: PROPERTY_VALID));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($value)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $value);
    }

    function get_survey_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
    }

    function set_survey_id($value)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_ID, $value);
    }

    function get_invitation_code()
    {
        return $this->get_default_property(self :: PROPERTY_INVITATION_CODE);
    }

    function set_invitation_code($value)
    {
        $this->set_default_property(self :: PROPERTY_INVITATION_CODE, $value);
    }

    function get_valid()
    {
        return $this->get_default_property(self :: PROPERTY_VALID);
    }

    function set_valid($value)
    {
        $this->set_default_property(self :: PROPERTY_VALID, $value);
    }

    function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    function set_email($value)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $value);
    }

    function get_data_manager()
    {
        return SurveyDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>