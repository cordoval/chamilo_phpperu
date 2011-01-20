<?php
namespace common\libraries;

use user\User;

/**
 * Basic AjaxManager class to handle AJAX calls
 * @author Hans De Bisschop
 * @package common.libraries
 */
abstract class AjaxManager
{
    /**
     * The user making the AJAX request
     * @var User
     */
    private $user;

    /**
     * An array of parameters as passed by the POST-request
     * @var array
     */
    private $parameters = array();

    /**
     * AjaxManager constructor
     * @param User $user
     */
    function __construct(User $user)
    {
        $this->user = $user;
        $this->validate_request();
    }

    /**
     * Run the AJAX component
     */
    abstract function run();

    /**
     * Return the current user's unique identifier
     * @return int
     */
    function get_user_id()
    {
        return $this->user->get_id();
    }

    /**
     * Gets the user.
     * @return User
     */
    function get_user()
    {
        return $this->user;
    }

    /**
     * Validate the AJAX call, if not validated,
     * trigger an HTTP 400 (Bad request) error
     */
    function validate_request()
    {
        foreach ($this->required_parameters() as $parameter)
        {
            $value = Request :: post($parameter);
            if (! is_null($value))
            {
                $this->set_parameter($parameter, $value);
            }
            else
            {
                JsonAjaxResult :: bad_request();
            }
        }
    }

    /**
     * Get the parameters
     * @return array
     */
    function get_parameters()
    {
        return $this->parameters;
    }

    /**
     * Set the parameters
     * @param array $parameters
     */
    function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the value of the given parameter.
     * @param string $name The parameter name.
     * @return string The parameter value.
     */
    function get_parameter($name)
    {
        if (array_key_exists($name, $this->parameters))
        {
            return $this->parameters[$name];
        }
    }

    /**
     * Sets the value of a parameter.
     * @param string $name The parameter name.
     * @param string $value The parameter value.
     */
    function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Get an array of parameters which should be
     * set for this call to work
     * @return array
     */
    abstract function required_parameters();

    /**
     * Create an instance of an AjaxManager and run it
     * @param User $user
     * @param string $context
     * @param string $method
     */
    static function launch(User $user, $context, $method)
    {
        self :: construct($user, $context, $method)->run();
    }

    static function construct(User $user, $context, $method)
    {
        $file = Path :: get(SYS_PATH) . Path :: namespace_to_path($context) . '/php/ajax/' . $method . '.class.php';

        if (! file_exists($file) || ! is_file($file))
        {
            JsonAjaxResult :: bad_request();
        }

        require_once $file;

        $class = $context . '\\' . Utilities :: get_package_name_from_namespace($context, true) . 'Ajax' . Utilities :: underscores_to_camelcase($method);
        return new $class($user);
    }
}
?>