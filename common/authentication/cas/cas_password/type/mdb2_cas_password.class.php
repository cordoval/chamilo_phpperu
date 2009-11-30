<?php
require_once dirname(__FILE__) . '/../cas_password.class.php';
require_once Path :: get_library_path() . 'webservice/webservice.class.php';

class Mdb2CasPassword extends CasPassword
{
    /**
     * Nothing is done with the password by default
     * This just prevents errors.
     *
     * @param String $old_password The user's old password
     * @param String $new_password The user's new password
     */
    function set_password($old_password, $new_password)
    {
        return true;
    }

    function is_password_changeable()
    {
        return false;
    }
}
?>