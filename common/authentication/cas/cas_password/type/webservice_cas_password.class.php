<?php
require_once dirname(__FILE__) . '/../cas_password.class.php';
require_once Path :: get_library_path() . 'webservice/webservice.class.php';

class WebserviceCasPassword extends CasPassword
{
    /**
     * Set the user's new password via a webservice.
     * Configuration depends on your backoffice:
     *
     * @param String $old_password The user's old password
     * @param String $new_password The user's new password
     */
    function set_password($old_password, $new_password)
    {
        // The URL of the webservice WSDL to be called
        $wsdl = 'http://www.mydomain.com/MyWebservice/myfile.asmx?WSDL';

        $function = array();
        // The service that will change the password
        $function['name'] = 'ChangePassword';

        // Change parameters according to your needs
        $parameters = array();
        $parameters['old_password'] = $old_password;
        $parameters['new_password'] = $new_password;
        $parameters['username'] = $this->get_user()->get_username();

        $function['parameters'] = $parameters;
        $function['handler'] = 'handle_webservice';

        $webservice = Webservice :: factory($this);
        return $webservice->call_webservice($wsdl, array($function));
    }

    /**
     * Handle the result of the webservice. Depending on your backoffice
     * you might have to change this. As is the function expects a
     * boolean True / False as a result.
     *
     * @param Array $result The array with the result of the webservice call
     * @return Boolean True or False
     */
    function handle_webservice($result)
    {
        return $result;
    }

    function is_password_changeable()
    {
        return true;
    }

    function get_password_requirements()
    {
        return Translation :: get('GeneralPasswordRequirements');
    }
}
?>