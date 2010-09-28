<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/viewer.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/mentor_rel_location_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/mentor_rel_user_browser/rel_user_browser_table.class.php';

class InternshipOrganizerOrganisationManagerMentorViewerComponent extends InternshipOrganizerOrganisationManager
{
    
    const TAB_LOCATIONS = 1;
    const TAB_USERS = 2;
    
    private $action_bar;
    private $mentor;

    function run()
    {
        
        $mentor_id = Request :: get(self :: PARAM_MENTOR_ID);
        $this->mentor = $this->retrieve_mentor($mentor_id);
        
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
        $parameters[self :: PARAM_ORGANISATION_ID] = $this->mentor->get_organisation_id();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_LOCATIONS;
        $table = new InternshipOrganizerMentorRelLocationBrowserTable($this, $parameters, $this->get_location_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('InternshipOrganizerLocations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_USERS;
        $table = new InternshipOrganizerMentorRelUserBrowserTable($this, $parameters, $this->get_user_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_USERS, Translation :: get('InternshipOrganizerUsers'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_MENTOR_ID => $this->mentor->get_id())));
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddLocations'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_subscribe_locations_url($this->mentor), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_subscribe_mentor_users_url($this->mentor), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        return $action_bar;
    }

    function get_user_condition()
    {
        
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $mentor_rel_user_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMentorRelUser :: get_table_name());
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(InternshipOrganizerMentorRelUser :: PROPERTY_MENTOR_ID, $this->mentor->get_id(), $mentor_rel_user_alias, true);
        
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

    function get_location_condition()
    {
        
        $query = $this->action_bar->get_query();
        $conditions = array();
        
        $mentor_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMentor :: get_table_name());
        $location_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerLocation :: get_table_name());
        $region_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerRegion :: get_table_name());
        
        $conditions[] = new EqualityCondition(InternshipOrganizerMentor :: PROPERTY_ID, $this->mentor->get_id(), $mentor_alias, true);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, '*' . $query . '*', $location_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*', $region_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*', $region_alias, true);
            
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_MENTORS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID, self :: PARAM_MENTOR_ID);
    }

}
?>