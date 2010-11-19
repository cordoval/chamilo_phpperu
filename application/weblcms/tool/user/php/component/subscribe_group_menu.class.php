<?php
namespace application\weblcms\tool\user;

use application\weblcms\CourseGroupSubscribeRight;
use HTML_Menu_ArrayRenderer;
use common\libraries\OptionsMenuRenderer;
use application\weblcms\WeblcmsManager;
use common\libraries\TreeMenuRenderer;
use group\GroupDataManager;
use HTML_Menu;
use common\libraries\Utilities;
use common\libraries\ObjectTableOrder;
use common\libraries\EqualityCondition;
use group\Group;

/**
 * $Id: group_menu.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Bart Mollet
 */
class SubscribeGroupMenu extends HTML_Menu
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

    private $current_category;

    private $show_complete_tree;

    private $hide_current_category;

    private $course;

    /**
     * Creates a new category navigation menu.
     * @param int $owner The ID of the owner of the categories to provide in
     * this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category.
     * Passed to sprintf(). Defaults to the string
     * "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     */
    function __construct($course, $cur_category, $url_format = '?application=group&go=browse&group=%s', $include_root = true, $show_complete_tree = false, $hide_current_category = false)
    {
        $this->course = $course;
        $this->include_root = $include_root;
        $this->show_complete_tree = $show_complete_tree;
        $this->hide_current_category = $hide_current_category;

        if ($cur_category == '0' || is_null($cur_category))
        {
            $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
            $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
            $this->current_category = $group;
        }
        else
        {
            $this->current_category = GroupDataManager :: get_instance()->retrieve_group($cur_category);
        }

        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_category->get_id()));
    }

    function get_menu()
    {
        $include_root = $this->include_root;

        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
        $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();

        if (! $include_root)
        {
            return $this->get_menu_items($group->get_id());
        }
        else
        {
            $menu = array();

            $menu_item = array();
            $menu_item['title'] = $group->get_name();
            $menu_item['url'] = $this->get_url($group->get_id());
            //$menu_item['url'] = $this->get_home_url($group->get_id());


            $sub_menu_items = $this->get_menu_items($group->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }

            $menu_item['class'] = 'home';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
            $menu[$group->get_id()] = $menu_item;
            return $menu;
        }
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
        $current_category = $this->current_category;

        $show_complete_tree = $this->show_complete_tree;
        $hide_current_category = $this->hide_current_category;

        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $parent_id);
        $groups = GroupDataManager :: get_instance()->retrieve_groups($condition, null, null, new ObjectTableOrder(Group :: PROPERTY_NAME));

        while ($group = $groups->next_result())
        {
            $group_id = $group->get_id();
            if (! ($group_id == $current_category->get_id() && $hide_current_category) && $this->course->can_group_subscribe($group_id) == CourseGroupSubscribeRight :: SUBSCRIBE_DIRECT)
            {
                $menu_item = array();
                $menu_item['title'] = $group->get_name();
                $menu_item['url'] = $this->get_url($group->get_id());

                if ($group->is_parent_of($current_category) || $group->get_id() == $current_category->get_id() || $show_complete_tree)
                {
                    if ($group->has_children())
                    {
                        $menu_item['sub'] = $this->get_menu_items($group->get_id());
                    }
                }
                else
                {
                    if ($group->has_children())
                    {
                        $menu_item['children'] = 'expand';
                    }
                }

                $menu_item['class'] = 'category';
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
                $menu[$group->get_id()] = $menu_item;
            }
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
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $group));
    }

    private function get_home_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&' . WeblcmsManager :: PARAM_GROUP . '=%s', '', $this->urlFmt));
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
        return Utilities :: get_classname_from_namespace(self :: TREE_NAME, true);
    }
}