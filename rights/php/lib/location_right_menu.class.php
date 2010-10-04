<?php
/**
 * $Id: location_right_menu.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Bart Mollet
 */
class LocationRightMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;
    
    private $include_root;
    
    private $exclude_children;
    
    private $root_category;
    
    private $current_category;

    /**
     * Creates a new category navigation menu.
     * @param int $owner The ID of the owner of the categories to provide in
     * this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category.
     *                           Passed to sprintf(). Defaults to the string
     *                           "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.
     */
    function LocationRightMenu($root_category, $current_category, $url_format = '?application=rights&go=browse&id=%s', $include_root = true, $exclude_children = false)
    {
        $this->include_root = $include_root;
        $this->exclude_children = $exclude_children;
        $this->root_category = $root_category;
        $this->current_category = $current_category;
        
        if ($current_category == '0')
        {
            $condition = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
            $location = RightsDataManager :: get_instance()->retrieve_locations($condition, null, 1, array(new ObjectTableOrder(Location :: PROPERTY_LOCATION)))->next_result();
            
            $this->current_category = $location->get_id();
        }
        
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_category));
    }

    function get_menu()
    {
        //$xtmr = new XmlTreeMenuRenderer($this);
        //return $xtmr->get_tree();
        

        $include_root = $this->include_root;
        $location = RightsDataManager :: get_instance()->retrieve_location($this->root_category);
        
        if (! $include_root)
        {
            return $this->get_menu_items($location->get_id());
        }
        else
        {
            $menu = array();
            
            $menu_item = array();
            $menu_item['title'] = $location->get_location();
            $menu_item['url'] = $this->get_url($location->get_id());
            
            $sub_menu_items = $this->get_menu_items($location->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            
            $menu_item['class'] = 'home';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $location->get_id();
            $menu[$location->get_id()] = $menu_item;
            return $menu;
        }
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_menu_items($parent_id = 0)
    {
        $exclude_children = $this->exclude_children;
        $current_category = $this->current_category;
        
        $condition = new EqualityCondition(Location :: PROPERTY_PARENT, $parent_id);
        $locations = RightsDataManager :: get_instance()->retrieve_locations($condition, null, null, array(new ObjectTableOrder(Location :: PROPERTY_LOCATION)));
        
        while ($location = $locations->next_result())
        {
            $location_id = $location->get_id();
            
            if (! ($exclude_children && $location_id == $current_category))
            {
                $menu_item = array();
                $menu_item['title'] = $location->get_location();
                $menu_item['url'] = $this->get_url($location->get_id());
                
                if ($location->has_children())
                {
                    $sub_menu_items = $this->get_menu_items($location->get_id());
                    
                    if (count($sub_menu_items) > 0)
                    {
                        $menu_item['sub'] = $sub_menu_items;
                    }
                    
                    $menu_item['class'] = 'category';
                }
                else
                {
                    $menu_item['class'] = 'end_node';
                }
                
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $location->get_id();
                $menu[$location->get_id()] = $menu_item;
            }
        }
        
        return $menu;
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    function get_url($location)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $location));
    }

    private function get_root_url()
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&location=%s', '', $this->urlFmt));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * @return array The breadcrumbs.
     */
    function get_breadcrumbs()
    {
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $crumb['name'] = $crumb['title'];
            unset($crumb['title']);
        }
        return $breadcrumbs;
    }

    /**
     * Renders the menu as a tree
     * @return string The HTML formatted tree
     */
	function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
    
    static function get_tree_name()
    {
    	return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }
}