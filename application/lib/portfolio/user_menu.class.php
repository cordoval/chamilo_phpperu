<?php
/**
 * $Id: user_menu.class.php 240 2009-11-16 14:34:39Z vanpouckesven $
 * @package application.portfolio.portfolio_manager.component
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through users
 * @author Sven Vanpoucke
 */
class UserMenu extends HTML_Menu
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
    function UserMenu($firstletter, $url_format = '?application=portfolio&go=browse&firstletter=%s')
    { 
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_menu_item_url($firstletter));
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
        
        $users = array();
        $users['title'] = Translation :: get('Users');
        $users['url'] = $this->get_menu_item_url(null);
        $users['class'] = 'home';
        $users['sub'] = $this->get_menu_items();
        $menu[] = $users;
        
        return $menu;
    }

    private function get_menu_items($parent_id)
    {
        $start_label = 'A';
        
        for($i = 0; $i < 9; $i ++)
        {
            $end_label = $start_label;
            $end_label ++;
            
            if ($i < 8)
                $end_label ++;
            
            $item['title'] = $start_label . ' - ' . $end_label;
            $item['url'] = $this->get_menu_item_url($start_label);
            $item['class'] = 'type_category';
            $tree[] = $item;
            
            $start_label = $end_label;
            $start_label ++;
        }
        
        return $tree;
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    private function get_menu_item_url($firstletter)
    {
        if (! $firstletter)
            return str_replace('&firstletter=%s', '', $this->urlFmt);
        
        return htmlentities(sprintf($this->urlFmt, $firstletter));
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
            if ($crumb['title'] == Translation :: get('Users'))
                continue;
            $trail->add(new Breadcrumb($crumb['url'], $crumb['title']));
        }
        return $trail;
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