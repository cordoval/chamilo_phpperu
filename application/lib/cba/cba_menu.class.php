<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';

require_once 'category_manager/competency_category.class.php';
require_once 'category_manager/indicator_category.class.php';
require_once 'category_manager/criteria_category.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Nick Van Loocke
 */
class CbaMenu extends HTML_Menu
{
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
     * Array to define the types on which the count on the categories should be filtered
     * Leave empty if you want to count everything
     * @var String[]
     */
    private $filter_count_on_types;

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
     * @param string[] $filter_count_on_types - Array to define the types on which the count on the categories should be filtered                         
     */
    function CbaMenu($owner, $current_category, $url_format = '?category=%s', $extra_items = array(), $filter_count_on_types = array())
    {
        $this->owner = $owner;
        $this->urlFmt = $url_format;
        $this->data_manager = CbaDataManager :: get_instance();
        
        $this->filter_count_on_types = $filter_count_on_types;
        
        $menu = $this->get_menu_items($extra_items);
        parent :: __construct($menu);
        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_category_competency_url($current_category));
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
        $menu = array();
        $menu_item = array();

        $conditionsCompetency[] = new EqualityCondition(Competency :: PROPERTY_STATE, Competency :: STATE_NORMAL);
        $conditionsCompetency[] = new EqualityCondition(Competency :: PROPERTY_PARENT_ID, 0);
        $conditionsCompetency[] = new EqualityCondition(Competency :: PROPERTY_OWNER_ID, $this->owner);
        
        $conditionsIndicator[] = new EqualityCondition(Indicator :: PROPERTY_STATE, Indicator :: STATE_NORMAL);
        $conditionsIndicator[] = new EqualityCondition(Indicator :: PROPERTY_PARENT_ID, 0);
        $conditionsIndicator[] = new EqualityCondition(Indicator :: PROPERTY_OWNER_ID, $this->owner);
        
        $conditionsCriteria[] = new EqualityCondition(Criteria :: PROPERTY_STATE, Criteria :: STATE_NORMAL);
        $conditionsCriteria[] = new EqualityCondition(Criteria :: PROPERTY_PARENT_ID, 0);
        $conditionsCriteria[] = new EqualityCondition(Criteria :: PROPERTY_OWNER_ID, $this->owner);
        
        $conditionCompetency = new AndCondition($conditionsCompetency);
        $conditionIndicator = new AndCondition($conditionsIndicator);
        $conditionCriteria = new AndCondition($conditionsCriteria);
        
        // Competency
        
        $count = $this->data_manager->count_competencys($conditionCompetency);

        $menu_item['title'] = Translation :: get('Competency') . ' (' . $count . ')';
        $menu_item['url'] = $this->get_category_competency_url(0);
        $sub_menu_items = $this->get_sub_competency_menu_items(0);
        if (count($sub_menu_items) >= 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        $menu_item['class'] = 'category';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[0] = $menu_item;
        
        
        // Indicator
        
        $count = $this->data_manager->count_indicators($conditionIndicator);
        
        $menu_item['title'] = Translation :: get('Indicator') . ' (' . $count . ')';
        $menu_item['url'] = $this->get_category_indicator_url(0);
        $sub_menu_items = $this->get_sub_indicator_menu_items(0);
        if (count($sub_menu_items) >= 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        $menu_item['class'] = 'category';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[1] = $menu_item;
        
        
        // Criteria
        
        $count = $this->data_manager->count_criterias($conditionCriteria);
        
        $menu_item['title'] = Translation :: get('Criteria') . ' (' . $count . ')';
        $menu_item['url'] = $this->get_category_criteria_url(0);
        $sub_menu_items = $this->get_sub_criteria_menu_items(0);
        if (count($sub_menu_items) >= 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        $menu_item['class'] = 'category';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
        $menu[2] = $menu_item;
        
        
        // See cba_manager.class.php for the other items
        
        if (count($extra_items))
        {
            $menu = array_merge($menu, $extra_items);
        }
		
        return $menu;
    }
    
    
	/**
     * Returns the items of the sub menu of the competency table.
     * @param array $categories The categories to include in this menu.
     * @param int $parent The parent category ID.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_sub_competency_menu_items($parent)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CompetencyCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);

        $objects = $this->data_manager->retrieve_competency_categories($condition);
        $categories = array();
        while ($category = $objects->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category->get_id());
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
        	
            $condition = new AndCondition($conditions);
            $count = $this->data_manager->count_competencys($condition);

            $menu_item = array();
            $menu_item['title'] = $category->get_name() . ' (' . $count . ')';
            $menu_item['url'] = $this->get_sub_category_competency_url($category->get_id());
            $sub_menu_items = $this->get_sub_competency_menu_items($category->get_id());
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
     * Returns the items of the sub menu of the indicator table.
     * @param array $categories The categories to include in this menu.
     * @param int $parent The parent category ID.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_sub_indicator_menu_items($parent)
    {
        $conditions = array();
        //$conditions[] = new EqualityCondition(CompetencyCategory :: PROPERTY_USER_ID, $this->owner);
        $conditions[] = new EqualityCondition(IndicatorCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);

        $objects = $this->data_manager->retrieve_indicator_categories($condition);
        $categories = array();
        while ($category = $objects->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category->get_id());
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
        	
            $condition = new AndCondition($conditions);
            $count = $this->data_manager->count_indicators($condition);

            $menu_item = array();
            $menu_item['title'] = $category->get_name() . ' (' . $count . ')';
            $menu_item['url'] = $this->get_sub_category_indicator_url($category->get_id());
            $sub_menu_items = $this->get_sub_indicator_menu_items($category->get_id());
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
     * Returns the items of the sub menu of the criteria table.
     * @param array $categories The categories to include in this menu.
     * @param int $parent The parent category ID.
     * @return array An array with all menu items. The structure of this array
     *               is the structure needed by PEAR::HTML_Menu, on which this
     *               class is based.
     */
    private function get_sub_criteria_menu_items($parent)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(CriteriaCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);

        $objects = $this->data_manager->retrieve_criteria_categories($condition);
        $categories = array();
        while ($category = $objects->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category->get_id());
        	
            $condition = new AndCondition($conditions);
            $count = $this->data_manager->count_criteria($condition);

            $menu_item = array();
            $menu_item['title'] = $category->get_name() . ' (' . $count . ')';
            $menu_item['url'] = $this->get_sub_category_criteria_url($category->get_id());
            $sub_menu_items = $this->get_sub_criteria_menu_items($category->get_id());
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
    private function get_category_competency_url($category)
    {
        //return htmlentities(sprintf($this->urlFmt, $category));
        $urlIndicator = '/chamilo/run.php?application=cba&go=competency&category=%s';
        return htmlentities(sprintf($urlIndicator, $category));
    }
    
	private function get_category_indicator_url($category)
    {
        $urlIndicator = '/chamilo/run.php?application=cba&go=indicator&category=%s';
        return htmlentities(sprintf($urlIndicator, $category));
    }
    
	private function get_category_criteria_url($category)
    {
        $urlCriteria = '/chamilo/run.php?application=cba&go=criteria&category=%s';
        return htmlentities(sprintf($urlCriteria, $category));
    }
    
    
    // Sub categories url's
    
	private function get_sub_category_competency_url($category)
    {
    	$subUrlCompetency = '/chamilo/run.php?application=cba&go=competency&category=%s';
        //$subUrlCompetency = '/chamilo/run.php?go=manage_categories_competency&application=cba&category_action=browse_categories&category=%s';
        return htmlentities(sprintf($subUrlCompetency, $category));
    }
    
	private function get_sub_category_indicator_url($category)
    {
        $subUrlIndicator = '/chamilo/run.php?go=manage_categories_indicator&application=cba&category_action=browse_categories&category=%s';
        return htmlentities(sprintf($subUrlIndicator, $category));
    }
    
	private function get_sub_category_criteria_url($category)
    {
        $subUrlCriteria = '/chamilo/run.php?go=manage_categories_criteria&application=cba&category_action=browse_categories&category=%s';
        return htmlentities(sprintf($subUrlCriteria, $category));
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
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
}
?>