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
    	$connection = MDB2 :: connect('dbms://user:password@server:port/database', array('debug' => 3));
    	$connection->setCharset('utf8');

        $props = array();
        $props['password'] = $new_password;
        
        $connection->loadModule('Extended');
        $condition = new EqualityCondition('username', $this->get_user()->get_username());
        $result = $connection->extended->autoExecute('password_table', $props, MDB2_AUTOQUERY_UPDATE, $condition);
    	
        return $result;
    }

    function is_password_changeable()
    {
        return true;
    }
}
?>