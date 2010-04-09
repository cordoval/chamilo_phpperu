<?php
/**
 * $Id: group_menu.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib
 */

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Sven Vanpoucke
 */
class CourseGroupUserMenu extends HTML_Menu
{
    CONST TREE_NAME = __CLASS__;
    
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     * The selected group
     * @var int
     */
    private $current_group;

    /**
     * The current course
     * @var Course
     */
    private $course;

    /**
     * Creates a new course group navigation menu.
     * @param int $owner The ID of the owner of the categories to provide in
     * this menu.
     * @param int $current_group The ID of the current group in the menu.
     * @param string $url_format The format to use for the URL of a category.
     *                           Passed to sprintf(). Defaults to the string
     *                           "?category=%s".
     */
    function CourseGroupUserMenu($course, $current_group, $url_format = '?application=weblcms&go=courseviewer&tool=user&course=%s&group=%s')
    {
        if ($current_group == '0' || is_null($current_group))
        {
            $this->current_group = 0;
        }
        else
        {
            $this->current_group = $current_group;
        }

        $this->course = $course;
        $this->urlFmt = $url_format;

        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_group));
    }

    function get_menu()
    {
        $group_relations = $this->course->get_subscribed_groups();
        $menu = array();

        $menu_item = array();
        $menu_item['title'] = Translation :: get('Course');
        $menu_item['url'] = $this->get_home_url();
        $menu_item['class'] = 'home';

        $sub_menu_items = array();

        foreach($group_relations as $group_relation)
        {
            $group = $group_relation->get_group_object();

            $sub_menu_item = array();
            $sub_menu_item['title'] = $group->get_name();
            $sub_menu_item['url'] = $this->get_url($group->get_id());
            $sub_menu_item['class'] = 'category';
            $sub_menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();

            $sub_menu_items[] = $sub_menu_item;
        }

        $menu_item['sub'] = $sub_menu_items;
        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[0] = $menu_item;

        return $menu;
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    function get_url($group)
    {
        return htmlentities(sprintf($this->urlFmt, $this->course->get_id(), $group));
    }

    private function get_home_url()
    {
        return htmlentities(sprintf(str_replace('&group=%s', '', $this->urlFmt), $this->course->get_id()));
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