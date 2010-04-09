<?php
/**
 * $Id: webservice_category_menu.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Bart Mollet
 */
class WebserviceCategoryMenu extends HTML_Menu
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
    function WebserviceCategoryMenu($current_category, $url_format = '?application=webservice&go=browse&webservice_category_id=%s')
    {
        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_category));
    }

    function get_menu()
    {
        $menu = array();
        
        $menu_item = array();
        $menu_item['title'] = Translation :: get('Webservices');
        $menu_item['url'] = $this->get_home_url();
        
        $sub_menu_items = $this->get_menu_items(0);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        
        $menu_item['class'] = 'home';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[0] = $menu_item;
        return $menu;
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
        $condition = new EqualityCondition(WebserviceCategory :: PROPERTY_PARENT, $parent_id);
        $objects = WebserviceDataManager :: get_instance()->retrieve_webservice_categories($condition, null, null, null);
        
        while ($object = $objects->next_result())
        {
            $menu_item = array();
            $menu_item['title'] = $object->get_name();
            $menu_item['url'] = $this->get_url($object->get_id());
            
            $sub_menu_items = $this->get_menu_items($object->get_id());
            
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            
            $menu_item['class'] = 'type_category';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $object->get_id();
            $menu[$object->get_id()] = $menu_item;
        }
        
        return $menu;
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    private function get_url($webserviceCategory)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $webserviceCategory));
    }

    private function get_home_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&webservice_category_id=%s', '', $this->urlFmt));
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