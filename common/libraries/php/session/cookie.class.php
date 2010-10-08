<?php
/**
 * $Id: cookie.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.session
 */
class Cookie
{

    function register($variable, $value, $expiration = '900')
    {
        setcookie($variable, $value, time() + $expiration, "", Path :: get(WEB_PATH));
        $_COOKIE[$variable] = $value;
    }

    function unregister($variable)
    {
        setcookie($variable, "", time() - 3600);
        $_COOKIE[$variable] = null;
        unset($GLOBALS[$variable]);
    }

    function destroy()
    {
        $cookies = $_COOKIE;
        foreach ($cookies as $key => $value)
        {
            setcookie($key, "", time() - 3600);
        }
        $_COOKIE = array();
    }

    function retrieve($variable)
    {
        return $_COOKIE[$variable];
    }

    function get_user_id()
    {
        return self :: retrieve('_uid');
    }
}
?>