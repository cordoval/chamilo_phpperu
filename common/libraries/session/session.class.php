<?php
/**
 * $Id: session.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.session
 */
class Session
{

    static function start($already_installed = true)
    {
        // TODO: This is not configurable during install, so why include it ?
        //global $storeSessionInDb;
        //if (is_null($storeSessionInDb))
        //{
        //	$storeSessionInDb = false;
        //}
        //if ($storeSessionInDb && function_exists('session_set_save_handler'))
        //{
        //	include_once (api_get_library_path().'/session_handler.class.php');
        //	$session_handler = new session_handler();
        //	@ session_set_save_handler(array ($session_handler, 'open'), array ($session_handler, 'close'), array ($session_handler, 'read'), array ($session_handler, 'write'), array ($session_handler, 'destroy'), array ($session_handler, 'garbage'));
        //}
        

        $session_key = Configuration :: get_instance()->get_parameter('general', 'security_key');
        if (is_null($session_key))
            $session_key = 'dk_sid';
        
        session_name($session_key);
        session_start();
        if ($already_installed)
        {
            /*if (empty($_SESSION['checkChamiloURL']))
            {
                $_SESSION['checkChamiloURL'] = Path :: get(WEB_PATH);
            }
            elseif ($_SESSION['checkChamiloURL'] != Path :: get(WEB_PATH))
            {
                self :: clear();
            }*/
        }
    }

    static function register($variable, $value)
    {
        //session_register($variable);
        $_SESSION[$variable] = $value;
    }

    static function unregister($variable)
    {
    	if(array_key_exists($variable, $_SESSION))
        {
        	$_SESSION[$variable] = null;
        	unset($GLOBALS[$variable]);
        }
    }

    static function clear()
    {
        session_regenerate_id();
        session_unset();
        $_SESSION = array();
    }

    static function destroy()
    {
        session_unset();
        $_SESSION = array();
        session_destroy();
    }

    static function retrieve($variable)
    {
        if(array_key_exists($variable, $_SESSION))
        {
    		return $_SESSION[$variable];
        }
    }

    static function get_user_id()
    {
        return self :: retrieve('_uid');
    }
}
?>