<?php

require_once (dirname(__FILE__) . '/../../survey_context.class.php');

class SurveyStudentContext extends SurveyContext
{
    
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_FIRSTNAME = 'firstname';
    const PROPERTY_LASTNAME = 'lastname';
    const PROPERTY_EMAIL = 'email';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_FIRSTNAME, self :: PROPERTY_LASTNAME, self :: PROPERTY_EMAIL);
    }

    function get_lastname()
    {
        return $this->get_additional_property(self :: PROPERTY_LASTNAME);
    }

    function get_firstname()
    {
        return $this->get_additional_property(self :: PROPERTY_FIRSTNAME);
    }

    function get_email()
    {
        return $this->get_additional_property(self :: PROPERTY_EMAIL);
    }

    function set_lastname($lastname)
    {
        $this->set_additional_property(self :: PROPERTY_LASTNAME, $lastname);
    }

    function set_firstname($firstname)
    {
        $this->set_additional_property(self :: PROPERTY_FIRSTNAME, $firstname);
    }

    function set_email($email)
    {
        $this->set_additional_property(self :: PROPERTY_EMAIL, $email);
    }

    static public function get_display_name()
    {
        return Translation :: get('Student');
    }

    static public function create_contexts_for_user($key, $key_type = self :: PROPERTY_USERNAME_KEY)
    {
        
        if ($key_type == self :: PROPERTY_USERNAME_KEY)
        {
            $dm = UserDataManager :: get_instance();
            $condition = new EqualityCondition(User :: PROPERTY_USERNAME, $user_name);
            $users = $dm->retrieve_users($condition);
            $user = $users->next_result();
            $contexts = array();
            for($i = 0; $i < 5; $i ++)
            {
                $context = new SurveyStudentContext();
                $context->set_name('student nr: ' . $i);
                $context->set_firstname($user->get_firstname() . $i);
                $context->set_lastname($user->get_lastname() . $i);
                $context->set_email($user->get_email() . $i);
                $context->create();
                $contexts[] = $context;
            }
            
            return $contexts;
        }else{
        	return array();
        }
    
    }

    static public function get_allowed_keys()
    {
        return array(self :: PROPERTY_USERNAME_KEY);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}

?>