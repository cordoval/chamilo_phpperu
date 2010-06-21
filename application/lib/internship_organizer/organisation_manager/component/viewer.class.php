<?php

//require_once dirname(__FILE__) . '/../organisation_manager.class.php';


//require_once dirname ( __FILE__ ) . '/rel_location_browser/rel_location_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/mentor_browser/browser_table.class.php';


class InternshipOrganizerOrganisationManagerViewerComponent extends InternshipOrganizerOrganisationManager
{
    
    const TAB_LOCATIONS = 0;
    const TAB_MENTORS = 1;
    
    private $action_bar;
    private $organisation;

    function run()
    {
        
        $organisation_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
        $this->organisation = $this->retrieve_organisation($organisation_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->organisation->get_name()));
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
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
        $parameters[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID] = $this->organisation->get_id();
        $table = new InternshipOrganizerLocationBrowserTable($this, $parameters, $this->get_organisation_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_LOCATIONS, Translation :: get('InternshipOrganizerLocations'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters = $this->get_parameters();
        $parameters[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID] = $this->organisation->get_id();
        $table = new InternshipOrganizerMentorBrowserTable($this, $parameters, $this->get_mentor_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_MENTORS, Translation :: get('InternshipOrganizerMentors'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerLocation'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_location_url($this->organisation), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerMentor'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_mentor_url($this->organisation), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $action_bar->set_search_url($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $this->organisation->get_id())));
        
        return $action_bar;
    }

    function get_organisation_condition()
    {
        
        $query = $this->action_bar->get_query();
        $conditions = array();
        $organisation_id = $this->organisation->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $organisation_id);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*');
            //$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_STREET, '*' . $query . '*' );
            //$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_STREET_NUMBER, '*' . $query . '*' );
            //$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_CITY, '*' . $query . '*' );
            

            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

    function get_mentor_condition()
    {
        
        $query = $this->action_bar->get_query();
        $conditions = array();
        $organisation_id = $this->organisation->get_id();
        $conditions[] = new EqualityCondition(InternshipOrganizerMentor :: PROPERTY_ORGANISATION_ID, $organisation_id);
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerMentor::PROPERTY_LASTNAME, '*' . $query . '*' );
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerMentor::PROPERTY_TITLE, '*' . $query . '*' );
            $search_conditions [] = new PatternMatchCondition ( InternshipOrganizerMentor::PROPERTY_EMAIL, '*' . $query . '*' );
            $conditions[] = new OrCondition($search_conditions);
        }
        return new AndCondition($conditions);
    }

}
?>