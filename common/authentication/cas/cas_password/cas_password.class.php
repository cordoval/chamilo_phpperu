<?php
abstract class CasPassword
{
    private $user;

    /**
     * Constructor
     */
    function CasPassword($user)
    {
        $this->set_user($user);
    }

    /**
     * Creates an instance of an cas password class
     * @param string $password_type
     * @return CasPassword An object of a class implementing this abstract
     * class.
     */
    function factory($password_type, $user)
    {
        $cas_password_class_file = dirname(__FILE__) . '/type/' . $password_type . '_cas_password.class.php';
        $cas_password_class = Utilities :: underscores_to_camelcase($password_type) . 'CasPassword';
        require_once $cas_password_class_file;
        return new $cas_password_class($user);
    }

    abstract function set_password($old_password, $new_password);

    abstract function is_password_changeable();

    abstract function get_password_requirements();

    function get_user()
    {
        return $this->user;
    }

    function set_user($user)
    {
        $this->user = $user;
    }
}
?>