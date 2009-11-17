<?php
/**
 * $Id: security.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.security
 */
class Security
{

    /**
     * This function tackles the XSS injections.
     *
     * Filtering for XSS is very easily done by using the htmlentities() function.
     * This kind of filtering prevents JavaScript snippets to be understood as such.
     * @param	string	The variable to filter for XSS
     * @return	string	Filtered string
     */
    function remove_XSS($variable)
    {
        // TODO: Should this be UTF-8 by default ?
        //return htmlentities($variable, ENT_QUOTES, 'UTF-8');
        

        $removers = array('<script' => '&lt;script', '</script>' => '&lt;\script&gt;', 'onunload' => '', 'onclick' => '', 'onload' => '', 'onUnload' => '', 'onClick' => '', 'onLoad' => '');
        
        if (is_array($variable))
        {
            $variable = self :: remove_XSS_recursive($variable);
        }
        else
        {
            foreach ($removers as $tag => $replace)
            {
                $variable = str_replace($tag, $replace, $variable);
            }
        }
        
        return $variable;
    }

    function remove_XSS_recursive($array)
    {
        foreach ($array as $key => $value)
        {
            $key2 = self :: remove_XSS($key);
            $value2 = (is_array($value)) ? self :: remove_XSS_recursive($value) : self :: remove_XSS($value);
            
            unset($array[$key]);
            $array[$key2] = $value2;
        }
        return $array;
    }

    /**
     * Gets the user agent in the session to later check it with check_ua() to prevent
     * most cases of session hijacking.
     * @return void
     */
    function get_ua()
    {
        Session :: register('sec_ua_seed', uniqid(rand(), true));
        Session :: register('sec_ua', Request :: server('HTTP_USER_AGENT') . Session :: retrieve('sec_ua_seed'));
    }

    /**
     * Checks the user agent of the client as recorder by get_ua() to prevent
     * most session hijacking attacks.
     * @return	bool	True if the user agent is the same, false otherwise
     */
    function check_ua()
    {
        $session_agent = Session :: retrieve('sec_ua');
        $current_agent = Request :: server('HTTP_USER_AGENT') . Session :: retrieve('sec_ua_seed');
        
        if (isset($session_agent) and $session_agent === $current_agent)
        {
            return true;
        }
        return false;
    }

    /**
     * This function sets a random token to be included in a form as a hidden field
     * and saves it into the user's session.
     * This later prevents Cross-Site Request Forgeries by checking that the user is really
     * the one that sent this form in knowingly (this form hasn't been generated from
     * another website visited by the user at the same time).
     * Check the token with check_token()
     * @return	string	Token
     */
    function get_token()
    {
        $token = Hashing :: hash(uniqid(rand(), true));
        Session :: register('sec_token', $token);
        return $token;
    }

    /**
     * This function checks that the token generated in get_token() has been kept (prevents
     * Cross-Site Request Forgeries attacks)
     * @param	string	The array in which to get the token ('get' or 'post')
     * @return	bool	True if it's the right token, false otherwise
     */
    function check_token($array = 'post')
    {
        $session_token = Session :: retrieve('sec_token');
        
        switch ($array)
        {
            case 'get' :
                $get_token = Request :: get('sec_token');
                if (isset($session_token) && isset($get_token) && $session_token === $get_token)
                {
                    return true;
                }
                return false;
            case 'post' :
                $post_token = Request :: post('sec_token');
                if (isset($session_token) && isset($post_token) && $session_token === $post_token)
                {
                    return true;
                }
                return false;
            default :
                if (isset($session_token) && isset($array) && $session_token === $array)
                {
                    return true;
                }
                return false;
        }
        // Just in case, don't let anything slip
        return false;
    }
}
?>