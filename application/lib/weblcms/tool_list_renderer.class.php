<?php
/**
 * $Id: tool_list_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
/**
 * Renderer to display a set of tools
 */
abstract class ToolListRenderer
{
    const TYPE_MENU = 'menu';
    const TYPE_SHORTCUT = 'shortcut';
    const TYPE_FIXED = 'fixed_location';
    
    /**
     * The parent application
     */
    private $parent;
    
    /**
     * The visible tools
     * @var Array Of Strings
     */
    private $visible_tools;

    /**
     * Constructor
     * @param WebLcms $parent The parent application
     */
    function ToolListRenderer($parent, $visible_tools)
    {
        $this->parent = $parent;
        $this->visible_tools = $visible_tools;
    }

    /**
     * Create a new tool list renderer
     * @param string $class The implementation of this abstract class to load
     * @param WebLcms $parent The parent application
     */
    static function factory($type, $parent, $visible_tools = array())
    {
        $type .= '_tool_list_renderer';
        $file = dirname(__FILE__) . '/tool_list_renderer/' . $type . '.class.php';
        
        if(!file_exists($file))
        {
        	throw new exception(Translation :: get('CanNotLoadToolListRenderer'));
        }
        
        require_once $file;
        $class = Utilities :: underscores_to_camelcase($type);
        return new $class($parent, $visible_tools);
    }

    /**
     * Gets the parent application
     * @return WebLcms
     */
    function get_parent()
    {
        return $this->parent;
    }
    
    function get_visible_tools()
    {
    	return $this->visible_tools;
    }

    /**
     * Displays the tool list.
     */
    abstract function display();
}
?>