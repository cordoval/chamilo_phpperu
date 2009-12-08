<?php
/**
 * $Id: cas_authentication.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * @package common.authentication.cas
 */
require_once dirname(__FILE__) . '/../authentication.class.php';
require_once dirname(__FILE__) . '/cas_password/cas_password.class.php';
require_once ('CAS.php');

/*
 * Extension enabling the CAS-authentication system.
 * The official phpCAS client is used for communication with the CAS server.
 *
 * More info on CAS and phpCAS can be found at:
 * - http://www.jasig.org/cas
 * - http://www.jasig.org/wiki/display/CASC/phpCAS
 *
 * Requirements
 * - SSL-connection to the CAS-server
 * - Provide this class with a link to your CAS certificate for server verification
 *
 * If you use attributes, the following have to be available:
 * - email
 * - last_name
 * - first_name
 *
 * Available settings:
 * - host (Address of the host, e.g. http://www.mycompany.com)
 * - port (Port CAS is running on, e.g. typically 443)
 * - uri (Possible subfolder CAS is located in)
 * - certificate (Path of CAS' certificate file)
 * - enable_log (Whether or not to enable the log)
 * - log (Path of the log file)
 *
 */

class CasAuthentication extends Authentication
{
    private $cas_settings;

    function CasAuthentication()
    {
    }

    public function check_login($user, $username, $password = null)
    {
        if (!phpCAS :: has_already_been_called())
        {
            self :: initialize_cas_client();
        }

        $user_id = phpCAS :: getUser();

        $udm = UserDataManager :: get_instance();
        if (! $udm->is_username_available($user_id))
        {
            $user = $udm->retrieve_user_info($user_id);
        }
        else
        {
            $user = $this->register_new_user($user_id);
        }

        if (get_class($user) == 'User')
        {
            Session :: register('_uid', $user->get_id());
            Events :: trigger_event('login', 'user', array('server' => $_SERVER, 'user' => $user));

            $request_uri = Session :: retrieve('request_uri');

            if ($request_uri)
            {
                $request_uris = explode("/", $request_uri);
                $request_uri = array_pop($request_uris);
                header('Location: ' . $request_uri);
            }

            $login_page = PlatformSetting :: get('page_after_login');
            if ($login_page == 'weblcms')
            {
                header('Location: run.php?application=weblcms');
            }
        }
        else
        {
            return false;
        }
    }

    public function is_password_changeable($user)
    {
    	if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));
            exit();
        }
        else
        {
        	$settings = $this->get_configuration();

        	if ($settings['allow_change_password'] == true)
        	{
		        $cas_password_type = $this->determine_cas_password_type();
		        $cas_password = CasPassword :: factory($cas_password_type, $user);
		        return $cas_password->is_password_changeable();
        	}
        	else
        	{
        		return false;
        	}
        }
    }

    /**
     * Always returns false as the user's password
     * is not stored in the Chamilo datasource.
     *
     * @return bool false
     */
    function change_password($user, $old_password, $new_password)
    {
        if (!self :: is_password_changeable($user))
        {
            return false;
        }
        else
        {
            $cas_password_type = $this->determine_cas_password_type();
            $cas_password = CasPassword :: factory($cas_password_type, $user);
            return $cas_password->set_password($old_password, $new_password);
        }
    }

    function get_password_requirements()
    {
        $cas_password_type = $this->determine_cas_password_type();
        $cas_password = CasPassword :: factory($cas_password_type, null);
        return $cas_password->get_password_requirements();
    }

    /**
     * Determine the authentication handler type used by CAS for this user.
     *
     * WARNING:
     * Determination of the authentication handler depends on your specific
     * configuration. This might be achieved by analysing the username,
     * email address or a specific attribute made available for this purpose.
     *
     * @param User $user
     * @return String The type of authentication handler
     */
    function determine_cas_password_type()
    {
        /**
         * Use this in case yhe type is determined via
         * a CAS attribute. Change the user attributes
         * key to whatever is defined in your CAS setup.
         */

        if (!phpCAS :: has_already_been_called())
        {
            self :: initialize_cas_client();
        }

        $user_attributes = phpCAS :: getAttributes();
        $authentication_type = $user_attributes['authentication_type'];

        if (!isset($authentication_type))
        {
            return 'default';
        }
        else
        {
            return $authentication_type;
        }
    }

    public function is_username_changeable()
    {
        return false;
    }

    public function can_register_new_user()
    {
        return true;
    }

    public function register_new_user($user_id)
    {
        if (!phpCAS :: has_already_been_called())
        {
            self :: initialize_cas_client();
        }

        $user_attributes = phpCAS :: getAttributes();

        $user = new User();
        $user->set_username($user_id);
        $user->set_password('PLACEHOLDER');
        $user->set_status(5);
        $user->set_auth_source('cas');
        $user->set_platformadmin(0);
        $user->set_language('english');
        $user->set_email($user_attributes['email']);
        $user->set_lastname($user_attributes['last_name']);
        $user->set_firstname($user_attributes['first_name']);

        if (! $user->create())
        {
            return false;
        }
        else
        {
            return $user;
        }
    }

    function logout($user)
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));
            exit();
        }
        else
        {
            if (!phpCAS :: has_already_been_called())
            {
                self :: initialize_cas_client();
            }

            // Do the logout
            phpCAS :: logout();

            Session :: destroy();
        }
    }

    function get_configuration()
    {
        if (! isset($this->cas_settings))
        {
            $cas = array();
            $cas['host'] = PlatformSetting :: get('cas_host');
            $cas['port'] = PlatformSetting :: get('cas_port');
            $cas['uri'] = PlatformSetting :: get('cas_uri');
            $cas['certificate'] = PlatformSetting :: get('cas_certificate');
            $cas['log'] = PlatformSetting :: get('cas_log');
            $cas['enable_log'] = PlatformSetting :: get('cas_enable_log');
            $cas['allow_change_password'] = PlatformSetting :: get('cas_allow_change_password');

            $this->cas_settings = $cas;
        }

        return $this->cas_settings;
    }

    function is_configured()
    {
        $settings = $this->get_configuration();

        foreach ($settings as $setting => $value)
        {
            if ((empty($value) || ! isset($value)) && ! in_array($setting, array('uri', 'certificate', 'log', 'enable_log')))
            {
                return false;
            }
        }

        return true;
    }

    function initialize_cas_client()
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));
            exit();
        }
        else
        {
            $settings = $this->get_configuration();

            // initialize phpCAS
            if ($settings['enable_log'])
            {
                phpCAS :: setDebug($settings['log']);
            }
            phpCAS :: client(SAML_VERSION_1_1, $settings['host'], (int) $settings['port'], '', true);

            // SSL validation for the CAS server
            $crt_path = $settings['certificate'];
            phpCAS :: setExtraCurlOption(CURLOPT_SSLVERSION, 3);
            phpCAS :: setCasServerCACert($crt_path);
            //phpCAS :: setNoCasServerValidation();

            // force CAS authentication
            phpCAS :: forceAuthentication();
        }
    }
}
?>