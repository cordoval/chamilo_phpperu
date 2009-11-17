<?php
/**
 * $Id: course_category_menu.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Bart Mollet
 */
class CourseCategoryMenu extends HTML_Menu
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
    function CourseCategoryMenu($current_category, $url_format = '?category=%s', $extra_items = array())
    {
        $this->urlFmt = $url_format;
        $menu = $this->get_menu_items($extra_items);
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
    private function get_menu_items($extra_items)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $usercategories = $wdm->retrieve_course_categories();
        $categories = array();
        while ($category = $usercategories->next_result())
        {
            $categories[$category->get_parent()][] = $category;
        }
        $menu = $this->get_sub_menu_items($categories, 0);
        if (count($extra_items))
        {
            $menu = array_merge($menu, $extra_items);
        }
        
        $home = array();
        $home['title'] = Translation :: get('Home');
        $home['url'] = $this->get_home_url();
        $home['class'] = 'home';
        $home_item[] = $home;
        $menu = array_merge($home_item, $menu);
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
    private function get_sub_menu_items($categories, $parent)
    {
        $sub_tree = array();
        foreach ($categories[$parent] as $index => $category)
        {
            $menu_item = array();
            
            $wdm = WeblcmsDataManager :: get_instance();
            $count = $wdm->count_courses(new EqualityCondition(Course :: PROPERTY_CATEGORY, $category->get_id()));
            
            $menu_item['title'] = $category->get_name() . ' (' . $count . ')';
            if (Request :: get(Application :: PARAM_ACTION) == WeblcmsManager :: ACTION_COURSE_CATEGORY_MANAGER)
            {
                $menu_item['url'] = $this->get_category_url($category->get_id());
            }
            else
            {
                $menu_item['url'] = $this->get_category_url($category->get_id());
            }
            $sub_menu_items = $this->get_sub_menu_items($categories, $category->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            $menu_item['class'] = 'type_category';
            $menu_item['node_id'] = $category->get_id();
            $sub_tree[$category->get_id()] = $menu_item;
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

    private function get_home_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&category=%s', '', $this->urlFmt));
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
        $this->render($renderer, 'tree');
        return $renderer->toHTML();
    }
}