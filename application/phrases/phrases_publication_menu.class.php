<?php
/**
 * $Id: content_object_category_menu.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__) . '/phrases_publication.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Bart Mollet
 */
class PhrasesPublicationMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;

    /**
     * The owner of the categories
     */
    private $owner;
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    private $data_manager;

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
     * @param string[] $filter_count_on_types - Array to define the types on which the count on the categories should be filtered
     */
    function PhrasesPublicationMenu($owner, $current_category, $url_format = '?level=%s&language=%s', $extra_items = array())
    {
        $this->owner = $owner;
        $this->urlFmt = $url_format;
        $this->data_manager = PhrasesDataManager :: get_instance();

        $menu = $this->get_menu_items($extra_items);
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        //        $this->forceCurrentUrl($this->get_category_url($current_category));
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

        $languages = AdminDataManager :: get_languages(false);

        foreach ($languages as $language_id => $language)
        {
            $menu_item = array();
            $menu_item['title'] = $language;
            //            $menu_item['url'] = $this->get_category_url($mastery_level->get_id());
            $menu_item['url'] = '#';
            $menu_item['class'] = 'category';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $language_id;

            $mastery_levels = $this->data_manager->retrieve_phrases_mastery_levels(null, new ObjectTableOrder(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER));

            $sub_menu = array();
            while ($mastery_level = $mastery_levels->next_result())
            {
                $sub_menu_item = array();
                $sub_menu_item['title'] = Translation :: get($mastery_level->get_level());
                //            $menu_item['url'] = $this->get_category_url($mastery_level->get_id());
                $sub_menu_item['url'] = '#';
                $sub_menu_item['class'] = 'category';
                $sub_menu_item[OptionsMenuRenderer :: KEY_ID] = $mastery_level->get_id();
                $sub_menu[] = $sub_menu_item;
            }

            $menu_item['sub'] = $sub_menu;

            $menu[] = $menu_item;
        }

        //        $menu_item = array();
        //
        //        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
        //        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, 0);
        //        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->owner);
        //        $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, LearningPathItem :: get_type_name()));
        //        $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, PortfolioItem :: get_type_name()));
        //
        //        if(count($this->filter_count_on_types))
        //        {
        //        	$conditions[] = new InCondition(ContentObject :: PROPERTY_TYPE, $this->filter_count_on_types);
        //        }
        //
        //        $condition = new AndCondition($conditions);
        //        $count = $this->data_manager->count_content_objects($condition);
        //
        //        $menu_item['title'] = Translation :: get('MyRepository') . ' (' . $count . ')';
        //        $menu_item['url'] = $this->get_category_url(0);
        //        $sub_menu_items = $this->get_sub_menu_items(0);
        //        if (count($sub_menu_items) > 0)
        //        {
        //            $menu_item['sub'] = $sub_menu_items;
        //        }
        //        $menu_item['class'] = 'category';
        //        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        //        $menu[0] = $menu_item;
        //        if (count($extra_items))
        //        {
        $menu = array_merge($menu, $extra_items);
        //        }


        return $menu;
    }

    /**
     * Returns the items of the sub menu.
     * @param array $categories The categories to include in this menu.
     * @param int $parent The parent category ID.
     * @return array An array with all menu items. The structure of this array
     * is the structure needed by PEAR::HTML_Menu, on which this
     * class is based.
     */
    private function get_sub_menu_items($parent)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->owner);
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);

        $objects = $this->data_manager->retrieve_categories($condition);
        $categories = array();
        while ($category = $objects->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category->get_id());
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->owner);
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
            $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, LearningPathItem :: get_type_name()));
            $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, PortfolioItem :: get_type_name()));

            if (count($this->filter_count_on_types))
            {
                $conditions[] = new InCondition(ContentObject :: PROPERTY_TYPE, $this->filter_count_on_types);
            }

            $condition = new AndCondition($conditions);

            $count = $this->data_manager->count_content_objects($condition);

            $menu_item = array();
            $menu_item['title'] = $category->get_name() . ' (' . $count . ')';
            $menu_item['url'] = $this->get_category_url($category->get_id());
            $sub_menu_items = $this->get_sub_menu_items($category->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            $menu_item['class'] = 'category';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $category->get_id();
            $categories[$category->get_id()] = $menu_item;
        }
        return $categories;
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
            $str = Translation :: get('Phrases');
            if (substr($crumb['title'], 0, strlen($str)) == $str)
            {
                continue;
            }
            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));

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
?>