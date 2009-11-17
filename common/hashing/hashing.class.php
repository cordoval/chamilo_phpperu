<?php
/**
 * $Id: hashing.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.hashing
 */
/**
 * Class that defines a hashing framework so people choose which hashing algorithm to use
 * @author vanpouckesven
 *
 */
abstract class Hashing
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'hashing_algorithm');
            $file = dirname(__FILE__) . '/' . $type . '/' . $type . '_hashing.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'Hashing';
            if (file_exists($file))
            {
                require_once ($file);
                self :: $instance = new $class();
            }
        }
        return self :: $instance;
    }

    static function hash($value)
    {
        $instance = self :: get_instance();
        return $instance->create_hash($value);
    }

    static function hash_file($file)
    {
        $instance = self :: get_instance();
        return $instance->create_file_has($file);
    }

    abstract function create_hash($value);

    abstract function create_file_hash($file);

}
?>