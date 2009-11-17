<?php
/**
 * $Id: request.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.session
 */
class Request
{

    function get($variable)
    {
        if (isset($_GET[$variable]))
        {
            $value = $_GET[$variable];
            // TODO: Add the necessary security filters if and where necessary
            $value = Security :: remove_XSS($value);
            return $value;
        }
        else
        {
            return null;
        }
    }

    function set_get($variable, $value)
    {
        $_GET[$variable] = $value;
    }

    function post($variable)
    {
        if (isset($_POST[$variable]))
        {
            $value = $_POST[$variable];
            // TODO: Add the necessary security filters if and where necessary
            return $value;
        }
        else
        {
            return null;
        }
    }

    function server($variable)
    {
        $value = $_SERVER[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }

    function file($variable)
    {
        $value = $_FILES[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }

    function environment($variable)
    {
        $value = $_ENV[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }
}
?>