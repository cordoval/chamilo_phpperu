<?php
/**
 * $Id: cas_authentication.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * @package common.authentication.cas
 */
require_once dirname(__FILE__) . '/../authentication.class.php';
require_once ('CAS.php');

class CasAuthentication extends Authentication
{
    private $cas_settings;

    function CasAuthentication()
    {
    }

    public function check_login($user, $username, $password = null)
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

            $user_id = phpCAS :: getUser();

            //            $user_attributes = phpCAS :: getAttributes();
            //
            //            dump($user_id);
            //            dump($user_attributes);
            //            exit;


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

    }

    public function is_password_changeable()
    {
        return true;
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

    public function register_new_user($user_id)
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));
            exit();
        }
        else
        {
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
}
?>