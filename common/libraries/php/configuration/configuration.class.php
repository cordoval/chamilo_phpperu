<?php
namespace common\libraries;
/**
 * $Id: configuration.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.configuration
 */

/**
 *	This class represents the current configuration.
 *
 *	@author Tim De Pauw
 */

class Configuration
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Parameters defined in the configuration. Stored as an associative array.
     */
    private $params;

    /**
     * Constructor.
     */
    private function  __construct()
    {
        global $configuration;
        // changed include_once to include because another part of the program
        // already have opened the file.
        include Path :: get_common_path(). 'configuration/configuration.php';
        $this->params = $configuration;
    }

    /**
     * Returns the instance of this class.
     * @return Configuration The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Gets a parameter from the configuration.
     * @param string $section The name of the section in which the parameter
     *                        is located.
     * @param string $name The parameter name.
     * @return mixed The parameter value.
     */
    function get_parameter($section, $name)
    {
        return $this->params[$section][$name];
    }
}
?>