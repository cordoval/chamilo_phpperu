<?php
/**
 * $Id: content_object_category_menu.class.php 204 2009-11-13 12:51:30Z tristan $
 * @package repository.lib
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

class RightsTreeRenderer extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    
    private $data_manager;
    
    private $groups;
    
    /**
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.                      
     */
    function RightsTreeRenderer($groups)
    {
		$this->groups = $groups;
        $menu = $this->get_menu_items();
        parent :: __construct($menu);
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_menu_items()
    {
        $menu = array();
    	$group = GroupDataManager :: get_instance()->retrieve_group(1);
        $menu_item = $this->get_group_array($group);
        $sub_menu_items = $this->get_sub_menu_items(1);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        $menu[] = $menu_item;
        return $menu;
    }

    /**
     * Returns the items of the sub menu.
     * @param array $categories The categories to include in this menu.
     * @param int $parent The parent category ID.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_sub_menu_items($parent)
    {
        $sub_menu = array();
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $parent);
        $sub_groups = GroupDataManager :: get_instance()->retrieve_groups($condition);
        while($group = $sub_groups->next_result())
        {
        	$sub_menu_item = $this->get_group_array($group);
        	$sub_sub_menu_items = $this->get_sub_menu_items($group->get_id());
		    if (count($sub_sub_menu_items) > 0)
		    {
		            $sub_menu_item['sub'] = $sub_sub_menu_items;
		    }
        	if(in_array($group->get_id(), $this->groups) || count($sub_sub_menu_items) > 0)
        		$sub_menu[] = $sub_menu_item;
        }
        return $sub_menu;
    }
    
    private function get_group_array($group)
    {
		$selected_group = array();
		$selected_group['id'] = 'group_' . $group->get_id();
		$selected_group['class'] = 'type type_group';
		$selected_group['title'] = $group->get_name();
		$selected_group['description'] = $group->get_name();
		return $selected_group;
    }
    

    /**
     * Renders the menu as a tree
     * @return string The HTML formatted tree
     */
	function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        $html = array();
        $html[] = '<div class="active_elements" style="overflow: auto; height: 300px; width: 310px;">';
        $html[] = $renderer->toHTML();
        $html[] = '</div>';
        return implode("\n", $html);
    }
    
    static function get_tree_name()
    {
    	return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }
}