<?php

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__) . '/category_manager/survey_publication_category.class.php';
require_once dirname(__FILE__) . '/survey_data_manager.class.php';

class SurveyPublicationCategoryMenu extends HTML_Menu
{
    
    const PARAM_CATEGORY = 'category';
    const TREE_NAME = __CLASS__;
    
    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;
    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;
    
    private $current_category;

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
    function SurveyPublicationCategoryMenu($current_category, $url_format = '?application=survey&go=browse_survey_publications&category=%s')
    {
        $this->current_category = $current_category;
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
        $menu_item['title'] = Translation :: get('Root') . ' (' . $this->get_publication_count(0) . ')';
        $menu_item['url'] = $this->get_url(0);
        
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
        $condition = new EqualityCondition(SurveyPublicationCategory :: PROPERTY_PARENT, $parent_id);
        $categories = SurveyDataManager :: get_instance()->retrieve_survey_publication_categories($condition, null, null, new ObjectTableOrder(SurveyPublicationCategory :: PROPERTY_DISPLAY_ORDER));
        
        while ($category = $categories->next_result())
        {
            
            $menu_item = array();
            $menu_item['title'] = $category->get_name() . ' (' . $this->get_publication_count($category->get_id()) . ')';
            $menu_item['url'] = $this->get_url($category->get_id());
            
            $sub_menu_items = $this->get_menu_items($category->get_id());
            
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            
            $menu_item['class'] = 'category';
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $category->get_id();
            $menu[$category->get_id()] = $menu_item;
        }
        
        return $menu;
    }

    function get_publication_count($category_id)
    {
        $conditions = array();
    	$conditions[] = new EqualityCondition(SurveyPublication :: PROPERTY_CATEGORY, $category_id);
        $conditions[] = new NotCondition(new EqualityCondition(SurveyPublication :: PROPERTY_TEST, 1 ));
        $condition = new AndCondition($conditions);
        return SurveyDataManager :: get_instance()->count_survey_publications($condition);
    }

    /**
     * Gets the URL of a given category
     * @param int $category The id of the category
     * @return string The requested URL
     */
    function get_url($category)
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
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        $trail = BreadcrumbTrail :: get_instance();
        $i = 0;
        foreach ($breadcrumbs as $crumb)
        {
            if ($i == 0)
            {
                $i ++;
                continue;
            }
            
            //$trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '(') - 1)));
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