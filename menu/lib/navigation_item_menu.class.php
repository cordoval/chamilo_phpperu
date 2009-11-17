<?php
/**
 * $Id: navigation_item_menu.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of encyclopedias.
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class NavigationItemMenu extends HTML_Menu
{
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

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
    function NavigationItemMenu($current_category, $url_format = '?category=%s', $extra_items_before = array(), $extra_items_after = array(), $condition = null)
    {
        $this->urlFmt = $url_format;
        $menu = $this->get_navigation_items($extra_items_before, $extra_items_after, $condition);
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_category_url($current_category));
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     *                           root.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_navigation_items($extra_items_before, $extra_items_after, $condition)
    {
        $datamanager = MenuDataManager :: get_instance();
        $objects = $datamanager->retrieve_navigation_items($condition, null, null, new ObjectTableOrder(NavigationItem :: PROPERTY_SORT));
        $categories = array();
        while ($category = $objects->next_result())
        {
            $categories[$category->get_category()][] = $category;
        }
        
        $home['title'] = Translation :: get('Home');
        $home['url'] = $this->get_category_url(0);
        $home['class'] = 'home';
        $home['sub'] = $this->get_sub_navigation_items($categories, 0);
        $home[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[0] = $home;
        
        if (count($extra_items_after))
        {
            $menu = array_merge($menu, $extra_items_after);
        }
        
        return $menu;
    }

    private function get_sub_navigation_items($categories, $parent)
    {
        foreach ($categories[$parent] as $index => $category)
        {
            if (count($categories[$category->get_id()]) > 0)
            {
                $navigation_item = array();
                $navigation_item['title'] = $category->get_title();
                $navigation_item['url'] = null;
                $navigation_item['url'] = $this->get_category_url($category->get_id());
                $sub_navigation_items = $this->get_sub_navigation_items($categories, $category->get_id());
                $navigation_item['sub'] = $sub_navigation_items;
                $navigation_item['class'] = 'type_category';
                $navigation_item[OptionsMenuRenderer :: KEY_ID] = $category->get_id();
                $sub_tree[] = $navigation_item;
            
            }
        }
        return $sub_tree;
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    private function get_category_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $category));
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
        $renderer = new TreeMenuRenderer();
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    function render_as_list()
    {
        $renderer = new OptionsMenuRenderer();
        $this->render($renderer, 'sitemap');
        $list = array('0' => Translation :: get('RootCategory')) + $renderer->toArray();
        return $list;
    }
}