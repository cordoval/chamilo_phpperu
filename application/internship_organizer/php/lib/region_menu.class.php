<?php
namespace application\internship_organizer;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\OptionsMenuRenderer;
use common\libraries\ObjectTableOrder;
use common\libraries\TreeMenuRenderer;

use HTML_Menu;
use HTML_Menu_ArrayRenderer;
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
class InternshipOrganizerRegionMenu extends HTML_Menu
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

    /**
     * Creates a new region navigation menu.
     * @param int $owner The ID of the owner of the regions to provide in
     * this menu.
     * @param int $current_region The ID of the current region in the menu.
     * @param string $url_format The format to use for the URL of a region.
     * Passed to sprintf(). Defaults to the string
     * "?region=%s".
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     */
    function __construct($current_region, $url_format = '?application=internship_organizer&go=region&region_id=%s', $include_root = true, $show_complete_tree = false, $hide_current_region = false)
    {
        $this->include_root = $include_root;
        $this->show_complete_tree = $show_complete_tree;
        $this->hide_current_region = $hide_current_region;

        if ($current_region == '0' || is_null($current_region))
        {
            $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, 0);
            $group = InternshipOrganizerDataManager :: get_instance()->retrieve_regions($condition, null, 1, new ObjectTableOrder(InternshipOrganizerRegion :: PROPERTY_CITY_NAME))->next_result();
            $this->current_region = $group;
        }
        else
        {
            $this->current_region = InternshipOrganizerDataManager :: get_instance()->retrieve_internship_organizer_region($current_region);
        }

        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_region->get_id()));
    }

    function get_menu()
    {
        $include_root = $this->include_root;

        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, 0);
        $group = InternshipOrganizerDataManager :: get_instance()->retrieve_regions($condition, null, 1, new ObjectTableOrder(InternshipOrganizerRegion :: PROPERTY_CITY_NAME))->next_result();

        if (! $include_root)
        {
            return $this->get_menu_items($group->get_id());
        }
        else
        {
            $menu = array();

            $menu_item = array();
            $menu_item['title'] = $group->get_city_name();
            //$menu_item['url'] = $this->get_url($group->get_id());
            $menu_item['url'] = $this->get_home_url();

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
        $current_region = $this->current_region;

        $show_complete_tree = $this->show_complete_tree;
        $hide_current_region = $this->hide_current_region;

        $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, $parent_id);
        $groups = InternshipOrganizerDataManager :: get_instance()->retrieve_regions($condition, null, null, new ObjectTableOrder(InternshipOrganizerRegion :: PROPERTY_CITY_NAME));

        while ($group = $groups->next_result())
        {
            $group_id = $group->get_id();

            if (! ($group_id == $current_region->get_id() && $hide_current_region))
            {
                $menu_item = array();
                $menu_item['title'] = $group->get_city_name();
                $menu_item['url'] = $this->get_url($group->get_id());

                if ($group->is_parent_of($current_region) || $group->get_id() == $current_region->get_id() || $show_complete_tree)
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

                $menu_item['class'] = 'region';
                $menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
                $menu[$group->get_id()] = $menu_item;
            }
        }

        return $menu;
    }

    /**
     * Gets the URL of a given region
     * @param int $region The id of the region
     * @return string The requested URL
     */
    function get_url($group)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $group));
    }

    private function get_home_url($region)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&region_id=%s', '', $this->urlFmt));
    }

    /**
     * Get the breadcrumbs which lead to the current region.
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
        $treename = $this->get_tree_name();
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    static function get_tree_name()
    {
        return Utilities :: get_classname_from_namespace(self :: TREE_NAME, true);
    }
}