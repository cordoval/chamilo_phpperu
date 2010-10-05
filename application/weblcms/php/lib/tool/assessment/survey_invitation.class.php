<?php
/**
 * $Id: survey_invitation.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment
 */
class SurveyInvitation
{
    
    const PROPERTY_ID = 'id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_INVITATION_CODE = 'invitation_code';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_VALID = 'valid';
    
    const TABLE_NAME = 'survey_invitation';
    
    private $default_properties;

    function SurveyInvitation($id = null, $default_properties = array())
    {
        $this->set_id($id);
        $this->default_properties = $default_properties;
    }

    function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_SURVEY_ID, self :: PROPERTY_INVITATION_CODE, self :: PROPERTY_EMAIL, self :: PROPERTY_VALID);
    }

    function set_default_property($name, $value)
    {
        $this->default_properties[$name] = $value;
    }

    function get_default_property($name)
    {
        return $this->default_properties[$name];
    }

    function get_default_properties()
    {
        return $this->default_properties;
    }

    function set_default_properties($properties)
    {
        $this->default_properties = $properties;
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($value)
    {
        $this->set_default_property(self :: PROPERTY_ID, $value);
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

    function create()
    {
        $dm = WeblcmsDataManager :: get_instance();
        return $dm->create_survey_invitation($this);
    }

    function delete()
    {
        $dm = WeblcmsDataManager :: get_instance();
        return $dm->delete_survey_invitation($this);
    }

    function update()
    {
        $dm = WeblcmsDataManager :: get_instance();
        $success = $dm->update_survey_invitation($this);
        if ($success)
        {
            return true;
        }
        return false;
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>