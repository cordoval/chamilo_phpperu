<?php

require_once dirname ( __FILE__ ) . '/subscribe_location_browser/subscribe_location_browser_table.class.php';


class InternshipPlannerCategoryManagerSubscribeLocationBrowserComponent extends InternshipPlannerCategoryManagerComponent
{
    private $category;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
       
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('InternshipPlannerCategoryList')));
        $trail->add_help('category subscribe locations');

        $category_id = Request :: get(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID);

        if (isset($category_id))
        {
            $this->category = $this->retrieve_category($category_id);
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_VIEW_CATEGORY, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category_id)), $this->category->get_name()));
        }

        $trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('AddLocations')));
      
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
        
        $parameters[InternshipPlannerCategoryManager :: PARAM_ACTION] = InternshipPlannerCategoryManager :: ACTION_SUBSCRIBE_LOCATION_BROWSER;
        $parameters[InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID ] = $this->category->get_id();
    	
        $table = new SubscribeLocationBrowserTable($this, $parameters, $this->get_subscribe_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_subscribe_condition()
    {
        $condition = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_CATEGORY_ID, Request :: get(InternshipPlannerCategoryRelLocation :: PROPERTY_CATEGORY_ID));

        $category_rel_locations = $this->retrieve_category_rel_locations($condition);

        $conditions = array();
        while ($category_rel_location = $category_rel_locations->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(InternshipPlannerLocation :: PROPERTY_ID, $category_rel_location->get_location_id()));
        }

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(InternshipPlannerLocation :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipPlannerLocation :: PROPERTY_CITY, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipPlannerLocation :: PROPERTY_STREET, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(InternshipPlannerLocation :: PROPERTY_STREET_NUMBER, '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }

        if (count($conditions) == 0){
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

        $action_bar->set_search_url($this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowInternshipPlannerCategory'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Application :: PARAM_ACTION => InternshipPlannerCategoryManager :: ACTION_BROWSE_CATEGORIES, InternshipPlannerCategoryManager :: PARAM_CATEGORY_ID => $category->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL)));

        return $action_bar;
    }
}
?>