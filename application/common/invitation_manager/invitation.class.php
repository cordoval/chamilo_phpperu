<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 */

class Invitation extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_DATE = 'date';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';
    const PROPERTY_CODE = 'code';
    const PROPERTY_PARAMETERS = 'parameters';
    const PROPERTY_ANONYMOUS = 'anonymous';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_MESSAGE = 'message';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_RIGHTS_TEMPLATES = 'rights_templates';
    const PROPERTY_USER_CREATED = 'user_created';

    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_APPLICATION, self :: PROPERTY_DATE, self :: PROPERTY_EXPIRATION_DATE, self :: PROPERTY_CODE, self :: PROPERTY_PARAMETERS, self :: PROPERTY_ANONYMOUS, self :: PROPERTY_TITLE, self :: PROPERTY_MESSAGE,
                self :: PROPERTY_EMAIL, self :: PROPERTY_RIGHTS_TEMPLATES, self :: PROPERTY_USER_CREATED));
    }

    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }

    function get_expiration_date()
    {
        return $this->get_default_property(self :: PROPERTY_EXPIRATION_DATE);
    }

    function set_expiration_date($expiration_date)
    {
        $this->set_default_property(self :: PROPERTY_EXPIRATION_DATE, $expiration_date);
    }

    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
    }

    function get_parameters()
    {
        return $this->get_default_property(self :: PROPERTY_PARAMETERS);
    }

    function set_parameters($parameters)
    {
        $this->set_default_property(self :: PROPERTY_PARAMETERS, $parameters);
    }

    function get_anonymous()
    {
        return $this->get_default_property(self :: PROPERTY_ANONYMOUS);
    }

    function set_anonymous($anonymous)
    {
        $this->set_default_property(self :: PROPERTY_ANONYMOUS, $anonymous);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_message()
    {
        return $this->get_default_property(self :: PROPERTY_MESSAGE);
    }

    function set_message($message)
    {
        $this->set_default_property(self :: PROPERTY_MESSAGE, $message);
    }

    function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    function set_email($email)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $email);
    }

    function get_rights_templates()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHTS_TEMPLATES);
    }

    function set_rights_templates($rights_templates)
    {
        $this->set_default_property(self :: PROPERTY_RIGHTS_TEMPLATES, $rights_templates);
    }

    function get_user_created()
    {
        return $this->get_default_property(self :: PROPERTY_USER_CREATED);
    }

    function set_user_created($user_created)
    {
        $this->set_default_property(self :: PROPERTY_USER_CREATED, $user_created);
    }

    function is_valid()
    {
        return (time() >= $this->get_expiration_date() || $this->get_expiration_date() == 0) && ! $this->get_user_created();
    }

    function is_anonymous()
    {
        return $this->get_anonymous();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function create()
    {
        $this->set_code(md5(uniqid()));
        parent :: create();
    }
}
?>