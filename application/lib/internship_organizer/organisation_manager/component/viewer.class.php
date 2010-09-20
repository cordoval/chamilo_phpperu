<?php

//require_once dirname(__FILE__) . '/../organisation_manager.class.php';


//require_once dirname ( __FILE__ ) . '/rel_location_browser/rel_location_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/mentor_browser/browser_table.class.php';
//require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/rel_mentor_browser/rel_mentor_browser_table.class.php';


require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/rel_user_browser/rel_user_browser_table.class.php';

class InternshipOrganizerOrganisationManagerViewerComponent extends InternshipOrganizerOrganisationManager
{
    
    const TAB_LOCATIONS = 0;
    const TAB_MENTORS = 1;
    const TAB_USERS = 2;
    
    private $action_bar;
    private $organisation;

    function run()
    {
        
        $organisation_id = $_GET[self :: PARAM_ORGANISATION_ID];
        $this->organisation = $this->retrieve_organisation($organisation_id);
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        
        echo '<div>';
        echo $this->get_tabs();
        echo '</div>';
        echo '</div>';
        $this->display_footer();
    }

    function get_tabs()
    {
        
        $html = array();
        $html[] = '<div>';
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        $parameters[self :: PARAM_ORGANISATION_ID] = $this->organisation->get_id();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_LOCATIONS;
        $table = new InternshipOrganizerLocationBrowserTable($this, $parameters, $this->get_location_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('InternshipOrganizerLocations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_MENTORS;
        $table = new InternshipOrganizerMentorBrowserTable($this, $parameters, $this->get_mentor_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_MENTORS, Translation :: get('InternshipOrganizerMentors'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_USERS;
        $table = new InternshipOrganizerOrganisationRelUserBrowserTable($this, $parameters, $this->get_user_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_USERS, Translation :: get('InternshipOrganizerUsers'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_view_organisation_url($this->organisation), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerLocation'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_location_url($this->organisation), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerMentor'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_mentor_url($this->organisation), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $this->organisation->get_id())));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_subscribe_users_url($this->organisation), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function get_location_condition()
    {
        
    	$region_alias = InternshipOrganizerDataManager::get_instance()->get_alias(InternshipOrganizerRegion :: get_table_name());
        $organisation_alias = InternshipOrganizerDataManager::get_instance()->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = InternshipOrganizerDataManager::get_instance()->get_alias(InternshipOrganizerLocation :: get_table_name());
    	
        $query = $this->action_bar->get_query();
        $conditions = array();
        $organisation_id = $this->organisation->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $organisation_id, $location_alias, true);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*', $location_alias, true);
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_DESCRIPTION, '*' . $query . '*' , $location_alias, true);
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_ADDRESS, '*' . $query . '*' , $location_alias, true);
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_TELEPHONE, '*' . $query . '*' , $location_alias, true);
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_EMAIL, '*' . $query . '*' , $location_alias, true);
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_FAX, '*' . $query . '*' , $location_alias, true);
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerRegion::PROPERTY_CITY_NAME, '*' . $query . '*' , $region_alias, true);
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerRegion::PROPERTY_ZIP_CODE, '*' . $query . '*' , $region_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function get_mentor_condition()
    {
        
        $mentor_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMentor :: get_table_name());
        $organisation_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(InternshipOrganizerOrganisation :: PROPERTY_ID, $this->organisation->get_id(), $organisation_alias, true);
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, '*' . $query . '*', $mentor_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_LASTNAME, '*' . $query . '*', $mentor_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_TITLE, '*' . $query . '*', $mentor_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_EMAIL, '*' . $query . '*', $mentor_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function get_user_condition()
    {
        
        $organisation_rel_user_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerOrganisationRelUser :: get_table_name());
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_ORGANISATION_ID, $this->organisation->get_id(), $organisation_rel_user_alias, true);
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', $user_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        
        }
        
        return new AndCondition($conditions);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID);
    }

}
?>