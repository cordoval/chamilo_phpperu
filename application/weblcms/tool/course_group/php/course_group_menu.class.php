<?php
namespace application\weblcms\tool\course_group;

use HTML_Menu;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;

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
class CourseGroupMenu extends HTML_Menu
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
     * Passed to sprintf(). Defaults to the string
     * "?category=%s".
     */
    function CourseGroupMenu($course, $current_group, $url_format = '?application=weblcms&go=course_viewer&tool=course_group&course=%s&course_group=%s')
    {
        if ($current_group == '0' || is_null($current_group))
        {
            $this->current_group = WeblcmsDataManager :: get_instance()->retrieve_course_group_root($course->get_id());
        }
        else
        {
            $this->current_group = WeblcmsDataManager :: get_instance()->retrieve_course_group($current_group);
        }

        $this->course = $course;
        $this->urlFmt = $url_format;

        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_group->get_id()));
    }

    function get_menu()
    {
        $course_group = WeblcmsDataManager :: get_instance()->retrieve_course_group_root($this->course->get_id());

        $menu = array();

        $menu_item = array();
        $menu_item['title'] = $course_group->get_name();
        $menu_item['url'] = $this->get_home_url();

        $sub_menu_items = $this->get_menu_items($course_group->get_id());
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }

        $menu_item['class'] = 'home';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = $course_group->get_id();
        $menu[$course_group->get_id()] = $menu_item;
        return $menu;
    }

    /**
     * Returns the menu items.
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($parent_id = 0)
    {
        $condition = new EqualityCondition(CourseGroup :: PROPERTY_PARENT_ID, $parent_id);
        $groups = WeblcmsDataManager :: get_instance()->retrieve_course_groups($condition);

        $current_group = $this->current_group;

        while ($group = $groups->next_result())
        {
            $menu_item = array();
            $menu_item['title'] = $group->get_name();
            $menu_item['url'] = $this->get_url($group->get_id());

            if ($group->has_children())
            {
                $menu_item['sub'] = $this->get_menu_items($group->get_id());
            }

            $menu_item['class'] = 'category';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
            $menu[$group->get_id()] = $menu_item;
        }

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
        return htmlentities(sprintf(str_replace('&course_group=%s', '', $this->urlFmt), $this->course->get_id()));
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