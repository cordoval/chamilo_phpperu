<?php
namespace common\libraries;
use \HTML_Menu_ArrayRenderer;
/**
 * $Id: options_menu_renderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.menu
 */
/**
 * Renderer which can be used to create an array of options to use in a select
 * list. The options are displayed in a hierarchical way in the select list.
 */
class OptionsMenuRenderer extends HTML_Menu_ArrayRenderer
{
    const KEY_ID = 'id';

    /**
     * Create a new OptionsMenuRenderer
     * @param array $exclude Which items should be excluded (based on the $key
     * value in the menu items). The whole submenu of which the elements of the
     * exclude array are the root elements will be excluded.
     */
    function __construct($exclude = array())
    {
        $exclude = is_array($exclude) ? $exclude : array($exclude);
        $this->exclude = $exclude;
    }

    /*
	 * Inherited
	 */
    function renderEntry($node, $level, $type)
    {
        // If this node is in the exclude list, add all its child-nodes to the exclude list
        if (in_array($node[self :: KEY_ID], $this->exclude))
        {
            foreach ($node['sub'] as $child_id => $child)
            {
                if (! in_array($child_id, $this->exclude))
                {
                    $this->exclude[] = $child_id;
                }
            }
        }
        //         else
        {
            unset($node['sub']);
            $node['level'] = $level;
            $node['type'] = $type;
            $this->_menuAry[] = $node;
        }
    }

    /**
     * Returns an array which can be used as a list of options in a select-list
     * of a form.
     */
    public function toArray()
    {
        $array = parent :: toArray();
        $choices = array();
        foreach ($array as $index => $item)
        {
            $prefix = '';
            if ($item['level'] > 0)
            {
                $prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $item['level'] - 1) . '&mdash; ';
            }
            $choices[$item[self :: KEY_ID]] = $prefix . $item['title'];
        }
        return $choices;
    }
}
?>