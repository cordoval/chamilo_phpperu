<?php

namespace application\personal_messenger;

use HTML_Menu;
use common\libraries\Translation;
use common\libraries\TreeMenuRenderer;
use common\libraries\Utilities;
use HTML_Menu_ArrayRenderer;
/**
 * $Id: personal_messenger_menu.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * personal messages.
 * @author Bart Mollet
 */
class PersonalMessengerMenu extends HTML_Menu
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
     * Creates a new navigation menu.
     * @param int $owner The ID of the owner.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category.
     * Passed to sprintf(). Defaults to the string
     * "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the
     * root.
     */
    function __construct($current_category, $url_format = '?folder=%s', $extra_items = array())
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
     * root.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_menu_items($extra_items)
    {
        $menu = array();
        if (count($extra_items))
        {
            $menu = array_merge($menu, $extra_items);
        }

        $menu_item = array();
        $menu_item['title'] = Translation :: get('Inbox');
        $menu_item['url'] = $this->get_category_url('inbox');
        $menu_item['class'] = 'type_category';
        $home_item[] = $menu_item;

        $menu_item = array();
        $menu_item['title'] = Translation :: get('Outbox');
        $menu_item['url'] = $this->get_category_url('outbox');
        $menu_item['class'] = 'type_category';
        $home_item[] = $menu_item;
        $menu = array_merge($home_item, $menu);
        return $menu;
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
        return htmlentities(str_replace('&folder=%s', '', $this->urlFmt));
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
?>