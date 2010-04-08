<?php
/**
 * $Id: xml_tree_menu_renderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.menu
 */
class XmlTreeMenuRenderer
{
    private $menu;
    private $include_parents;
    private $include_siblings;
    private $include_children;

    function XmlTreeMenuRenderer($menu, $include_children = true, $include_parents = false, $include_siblings = false)
    {
        $this->menu = $menu;
        $this->include_children = $include_children;
        $this->include_parents = $include_parents;
        $this->include_siblings = $include_siblings;
    }

    function get_menu()
    {
        return $this->menu;
    }

    function get_category()
    {
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
        return GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_SORT))->next_result();
        
    //return $this->get_menu()->get_current_category();
    }

    function get_tree()
    {
        $menu = $this->get_menu();
        $category = $this->get_category();
        $children = $category->get_children(false);
        
        $tree = array();
        
        $root = array();
        $root['title'] = $category->get_name();
        $root['url'] = $menu->get_url($category->get_id());
        $root['class'] = 'type_category';
        $root['id'] = $category->get_id();
        
        $tree[$category->get_id()] = $root;
        
        while ($child = $children->next_result())
        {
            $ch = array();
            $ch['title'] = $child->get_name();
            $ch['url'] = $menu->get_url($child->get_id());
            $ch['class'] = 'type_category' . ($child->has_children() ? ' expandable' : '');
            $ch['id'] = $child->get_id();
            
            $tree[$category->get_id()]['sub'][$child->get_id()] = $ch;
        }
        
        return $tree;
    }
}
?>