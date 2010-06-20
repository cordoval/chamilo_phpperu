<?php

require_once dirname(__FILE__) . '/subscribe_location_browser/subscribe_location_browser_table.class.php';

class InternshipOrganizerCategoryManagerSubscribeLocationBrowserComponent extends InternshipOrganizerCategoryManager
{
    private $category;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        
        $trail->add(new Breadcrumb($this->get_browse_categories_url(), Translation :: get('BrowseInternshipOrganizerCategories')));
        
        $category_id = Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID);
        $this->category = $this->retrieve_category($category_id);
        
        $trail->add(new Breadcrumb($this->get_category_viewing_url($this->category), $this->category->get_name()));
        
        $trail->add(new Breadcrumb($this->get_category_subscribe_location_browser_url($this->category), Translation :: get('AddInternshipOrganizerLocation')));
        
        $trail->add_help('category subscribe locations');
        
        $this->ab = $this->get_action_bar();
        $output = $this->get_location_subscribe_html();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_location_subscribe_html()
    {
        $parameters = $this->get_parameters();
        
        $parameters[InternshipOrganizerCategoryManager :: PARAM_ACTION] = InternshipOrganizerCategoryManager :: ACTION_SUBSCRIBE_LOCATION_BROWSER;
        $parameters[InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID] = $this->category->get_id();
        
        $table = new SubscribeLocationBrowserTable($this, $parameters, $this->get_subscribe_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_subscribe_condition()
    {
        $condition = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, Request :: get(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID));
        
        $category_rel_locations = $this->retrieve_category_rel_locations($condition);
        
        $conditions = array();
        
        while ($category_rel_location = $category_rel_locations->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ID, $category_rel_location->get_location_id()));
        }
        
        $query = $this->ab->get_query();
        
        if (isset($query) && $query != '')
        {
            
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*');
            
            $search_city_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
            $search_city_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
            $city_conditions = new OrCondition($search_city_conditions);
            
            $search_city_subselect_condition = new SubselectCondition(InternshipOrganizerLocation :: PROPERTY_REGION_ID, InternshipOrganizerRegion :: PROPERTY_ID, InternshipOrganizerRegion :: get_table_name(), $city_conditions);
            $or_conditions[] = $search_city_subselect_condition;
            
            $conditions[] = new OrCondition($or_conditions);
        }
        
        if (count($conditions) == 0)
        {
            return null;
        }
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    function get_category()
    {
        return $this->category;
    }

    function get_action_bar()
    {
        $category = $this->category;
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_category_subscribe_location_browser_url($category));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_category_subscribe_location_browser_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>