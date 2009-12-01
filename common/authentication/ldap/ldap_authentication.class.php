<?php
/**
 * $Id: ldap_authentication.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.authentication.ldap
 */
require_once dirname(__FILE__) . '/../authentication.class.php';
/**
 * This authentication class uses LDAP to authenticate users.
 * When you want to use LDAP, you might want to change this implementation to
 * match your institutions LDAP structure. You may consider to copy the ldap-
 * directory to something like myldap and to rename the class files. Then you
 * can change your LDAP-implementation without changing this default. Please
 * note that the users in your database should have myldap as auth_source also
 * in that case.
 */
class LdapAuthentication extends Authentication
{
    private $ldap_settings;

    /**
     * Constructor
     */
    function LdapAuthentication()
    {
        $this->get_configuration();
    }

    public function check_login($user, $username, $password = null)
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckLDAPConfiguration'));
            exit();
        }
        else
        {
            $settings = $this->get_configuration();

            //include dirname(__FILE__).'/ldap_authentication_config.inc.php';
            $ldap_connect = ldap_connect($settings['host'], $settings['port']);
            if ($ldap_connect)
            {
                ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
                $filter = "(uid=$username)";
                $result = ldap_bind($ldap_connect, $settings['rdn'], $settings['password']);
                $search_result = ldap_search($ldap_connect, $settings['search_dn'], $filter);
                $info = ldap_get_entries($ldap_connect, $search_result);
                $dn = ($info[0]["dn"]);
                ldap_close($ldap_connect);
            }
            if ($dn == '')
            {
                return false;
            }
            if ($password == '')
            {
                return false;
            }
            $ldap_connect = ldap_connect($settings['host'], $settings['port']);
            ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
            if (! (@ldap_bind($ldap_connect, $dn, $password)) == true)
            {
                ldap_close($ldap_connect);
                return false;
            }
            else
            {
                ldap_close($ldap_connect);
                return true;
            }
        }
    }

    public function is_password_changeable($user)
    {
        return false;
    }

    /**
     * Always returns false as the user's password
     * is not stored in the Chamilo datasource.
     *
     * @return bool false
     */
    function change_password($user, $old_password, $new_password)
    {
    	return false;
    }

    public function is_username_changeable()
    {
        return false;
    }

    public function can_register_new_user()
    {
        return true;
    }

    public function register_new_user($username, $password = null)
    {
        if ($this->check_login(null, $username, $password))
        {
            $settings = $this->get_configuration();

            include dirname(__FILE__) . '/ldap_parser.class.php';
            $ldap_connect = ldap_connect($settings['host'], $settings['port']);
            if ($ldap_connect)
            {
                ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
                $ldap_bind = ldap_bind($ldap_connect, $settings['rdn'], $settings['password']);
                $filter = "(uid=$username)";
                $search_result = ldap_search($ldap_connect, $settings['search_dn'], $filter);
                $info = ldap_get_entries($ldap_connect, $search_result);

                $parser = new LdapParser();
                return $parser->parse($info, $username);
            }
            ldap_close($ldap_connect);
        }
        return false;
    }

    function get_configuration()
    {
        if (! isset($this->ldap_settings))
        {
            $ldap = array();
            $ldap['host'] = PlatformSetting :: get('ldap_host');
            $ldap['port'] = PlatformSetting :: get('ldap_port');
            $ldap['rdn'] = PlatformSetting :: get('ldap_remote_dn');
            $ldap['password'] = PlatformSetting :: get('ldap_password');
            $ldap['search_dn'] = PlatformSetting :: get('ldap_search_dn');

            $this->ldap_settings = $ldap;
        }

        return $this->ldap_settings;
    }
}
?>