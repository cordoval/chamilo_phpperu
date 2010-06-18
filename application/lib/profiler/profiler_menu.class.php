<?php
/**
 * $Id: profiler_menu.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__) . '/category_manager/profiler_category.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of profiles.
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class ProfilerMenu extends HTML_Menu
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
    function ProfilerMenu($current_category, $url_format = '?application=profiler&go=browse&category=%s')
    {
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
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
    private function get_menu()
    {
        $menu = array();
        
        $home = array();
        $home['title'] = Translation :: get('Home');
        $home['url'] = $this->get_category_url(0);
        $home['class'] = 'home';
        $home['sub'] = $this->get_menu_items(0);
        $menu[] = $home;
        
        return $menu;
    }

    private function get_menu_items($parent_id)
    {
        $pdm = ProfilerDataManager :: get_instance();
        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_PARENT, $parent_id);
        $categories = $pdm->retrieve_categories($condition);
        
        while ($category = $categories->next_result())
        {
            $item['title'] = $category->get_name();
            $item['url'] = $this->get_category_url($category->get_id());
            $item['class'] = 'type_category';
            $item['sub'] = $this->get_menu_items($category->get_id());
            $tree[] = $item;
        }
        
        return $tree;
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
        $trail = BreadcrumbTrail :: get_instance();
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            if ($crumb['title'] == Translation :: get('Home'))
                continue;
            $trail->add(new Breadcrumb($crumb['url'], $crumb['title']));
        }
        return $trail;
        //        $this->render($this->array_renderer, 'urhere');
    //        $breadcrumbs = $this->array_renderer->toArray();
    //        foreach ($breadcrumbs as $crumb)
    //        {
    //            $crumb['name'] = $crumb['title'];
    //            unset($crumb['title']);
    //        }
    //        return $breadcrumbs;
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