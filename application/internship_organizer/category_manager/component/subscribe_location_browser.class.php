<?php

require_once dirname(__FILE__) . '/subscribe_location_browser/subscribe_location_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/category_manager/component/browser.class.php';

class InternshipOrganizerCategoryManagerSubscribeLocationBrowserComponent extends InternshipOrganizerCategoryManager
{
    private $category;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $category_id = Request :: get(self :: PARAM_CATEGORY_ID);
        $this->category = $this->retrieve_category($category_id);
        
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
        
        $parameters[self :: PARAM_ACTION] = self :: ACTION_SUBSCRIBE_LOCATION_BROWSER;
        $parameters[self :: PARAM_CATEGORY_ID] = $this->category->get_id();
        
        $table = new SubscribeLocationBrowserTable($this, $parameters, $this->get_subscribe_condition(), $this->category);
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_subscribe_condition()
    {
        $condition = new EqualityCondition(InternshipOrganizerCategoryRelLocation :: PROPERTY_CATEGORY_ID, Request :: get(self :: PARAM_CATEGORY_ID));
        
        $category_rel_locations = $this->retrieve_category_rel_locations($condition);
        
        $conditions = array();
        
        while ($category_rel_location = $category_rel_locations->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ID, $category_rel_location->get_location_id()));
        }
        
        $query = $this->ab->get_query();
        if (isset($query) && $query != '')
        {
            
            $region_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerRegion :: get_table_name());
            $organisation_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerOrganisation :: get_table_name());
            $location_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerLocation :: get_table_name());
            
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_EMAIL, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_TELEPHONE, '*' . $query . '*', $location_alias, true);
            
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*', $region_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION, '*' . $query . '*', $region_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*', $region_alias, true);
            
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION, '*' . $query . '*', $organisation_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerOrganisation :: PROPERTY_NAME, '*' . $query . '*', $organisation_alias, true);
            
            $conditions[] = new OrCondition($search_conditions);
        }
        
        if (count($conditions) == 0)
        {
            return null;
        }
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    function get_action_bar()
    {
        $category = $this->category;
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_category_subscribe_location_browser_url($category));
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_category_subscribe_location_browser_url($category), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => Request :: get(self :: PARAM_CATEGORY_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerCategoryManagerBrowserComponent :: TAB_LOCATIONS)), Translation :: get('BrowseInternshipOrganizerCategories')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CATEGORY_ID);
    }

}
?>