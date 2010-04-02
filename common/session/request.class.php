<?php
/**
 * $Id: request.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.session
 */
class Request
{

	static function get($variable)
	{
		if (isset($_GET[$variable]))
		{
			// TODO: Add the necessary security filters if and where necessary
			return Security :: remove_XSS($_GET[$variable]);
		}

		return null;
	}

	static function set_get($variable, $value)
	{
		$_GET[$variable] = $value;
	}

	static function post($variable)
	{
		if (isset($_POST[$variable]))
		{
			// TODO: Add the necessary security filters if and where necessary
			return Security :: remove_XSS($_POST[$variable]);
		}

		return null;
	}

	static function server($variable)
	{
		$value = $_SERVER[$variable];
		// TODO: Add the necessary security filters if and where necessary
		return $value;
	}

	static function file($variable)
	{
		$value = $_FILES[$variable];
		// TODO: Add the necessary security filters if and where necessary
		return $value;
	}

	static function environment($variable)
	{
		$value = $_ENV[$variable];
		// TODO: Add the necessary security filters if and where necessary
		return $value;
	}
}
?>