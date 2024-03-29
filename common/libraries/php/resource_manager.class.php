<?php
namespace common\libraries;
/**
 * $Id: resource_manager.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */
/**
 * Manages resources, ensuring that they are only loaded when necessary.
 * Currently only relevant for JavaScript and CSS files.
 * @author Tim De Pauw
 */
class ResourceManager
{
    private static $instance;
    
    private $resources;

    private function __construct()
    {
        $this->resources = array();
    }
    
    function get_resources()
    {
    	return $this->resources;
    }

    function resource_loaded($path)
    {
        //return false;
        return in_array($path, $this->resources);
    }

    function get_resource_html($path)
    {
        if ($this->resource_loaded($path))
        {
            return '';
        }
        else
        {
            $this->resources[] = $path;
            return $this->_get_resource_html($path);
        }
    }

    private function _get_resource_html($path)
    {
        $matches = array();
        preg_match('/[^.]*$/', $path, $matches);
        $extension = $matches[0];
        switch (strtolower($extension))
        {
            case 'css' :
                return '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($path) . '"/>';
            case 'js' :
                return '<script type="text/javascript" src="' . htmlspecialchars($path) . '"></script>';
            default :
                die('Unknown resource type: ' . $path);
        }
    }

    /**
     * @return ResourceManager
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new ResourceManager();
        }
        return self :: $instance;
    }
}

?>