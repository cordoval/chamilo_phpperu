<?php
/**
 * $Id: toolbar.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.toolbar
 */
class Toolbar
{
    const TYPE_HORIZONTAL = 'horizontal';
    const TYPE_VERTICAL = 'vertical';
    
    private $items = array();
    private $class_names = array();
    private $css = null;

    function Toolbar($type = self :: TYPE_HORIZONTAL, $class_names = array(), $css = null)
    {
        $this->type = $type;
        $this->class_names = $class_names;
        $this->css = $css;
    }

    function set_items($items)
    {
        $this->items = $items;
    }

    function add_item($item)
    {
        $this->items[] = $item;
    }

    function add_items($items)
    {
        foreach ($items as $item)
        {
            $this->items[] = $item;
        }
    }

    function set_type($type)
    {
        $this->type = $type;
    }

    function get_type()
    {
        return $this->type;
    }

    function as_html()
    {
        $toolbar_data = $this->items;
        $type = $this->get_type();
        $class_names = $this->class_names;
        $css = $this->css;
        
        if (! is_array($class_names))
        {
            $class_names = array($class_names);
        }
        $class_names[] = 'toolbar_' . $type;
        
        $html = array();
        $html[] = '<div class="toolbar">';
        $html[] = '<ul class="' . implode(' ', $class_names) . '"' . (isset($css) ? ' style="' . $css . '"' : '') . '>';
        
        foreach ($toolbar_data as $index => $toolbar_item)
        {
            $classes = array();
            
            if ($index == 0)
            {
                $classes[] = 'first';
            }
            
            if ($index == count($toolbar_data) - 1)
            {
                $classes[] = 'last';
            }
            
            $html[] = '<li' . (count($classes) ? ' class="' . implode(' ', $classes) . '"' : '') . '>' . $toolbar_item->as_html() . '</li>';
        }
        
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        return implode($html);
    }
}
?>