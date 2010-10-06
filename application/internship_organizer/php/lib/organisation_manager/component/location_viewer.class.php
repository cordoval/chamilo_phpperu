<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/viewer.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'publisher/publication_table/publication_table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_manager/component/mentor_browser/browser_table.class.php';

class InternshipOrganizerOrganisationManagerLocationViewerComponent extends InternshipOrganizerOrganisationManager
{
    const TAB_PUBLICATIONS = 1;
    const TAB_MENTORS = 2;
    const TAB_DETAILS = 3;
    
    private $action_bar;
    
    private $location_id;
    private $location;
    private $region;

    function run()
    {
        
        $this->location_id = $_GET[self :: PARAM_LOCATION_ID];
        $this->location = $this->retrieve_location($this->location_id);
        $location = $this->location;
        $this->region = InternshipOrganizerDataManager::get_instance()->retrieve_region($location->get_region_id());
        
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
        $parameters[self :: PARAM_LOCATION_ID] = $this->location->get_id();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_PUBLICATIONS;
        $table = new InternshipOrganizerPublicationTable($this, $parameters, $this->get_publications_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_PUBLICATIONS, Translation :: get('InternshipOrganizerPublications'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_MENTORS;
        $table = new InternshipOrganizerMentorBrowserTable($this, $parameters, $this->get_mentor_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_MENTORS, Translation :: get('InternshipOrganizerMentors'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: TAB_DETAILS;
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAILS, Translation :: get('Details'), Theme :: get_image_path('internship_organizer') . 'place_mini_period.png', $this->get_detail()));
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_detail()
    {
        
        $html = array();
        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_location.png);">';
        $html[] = '<div class="title">' . Translation :: get('Details') . '</div>';
        $html[] = '<b>' . Translation :: get('Name') . '</b>: ' . $this->location->get_name();
        $html[] = '<br /><b>' . Translation :: get('Address') . '</b>: ' . $this->location->get_address();
        $html[] = '<br /><b>' . Translation :: get('ZipCode') . '</b>: ' . $this->region->get_zip_code();
        $html[] = '<br /><b>' . Translation :: get('City') . '</b>: ' . $this->region->get_city_name();
        $html[] = '<br /><b>' . Translation :: get('Telephone') . '</b>: ' . $this->location->get_telephone();
        $html[] = '<br /><b>' . Translation :: get('Fax') . '</b>: ' . $this->location->get_fax();
        $html[] = '<br /><b>' . Translation :: get('Email') . '</b>: ' . $this->location->get_email();
        $html[] = '<br /><b>' . Translation :: get('Description') . '</b>: ' . $this->location->get_description();
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_LOCATION_ID => $this->location->get_id())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_view_location_url($this->location), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
    	if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $this->location_id, InternshipOrganizerRights :: TYPE_LOCATION))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_location_publish_url($this->location_id), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

    function get_publications_condition()
    {
        $conditions = array();
        $publication_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerPublication :: get_table_name());
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_PLACE, InternshipOrganizerPublicationPlace :: LOCATION, $publication_alias, true);
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication :: PROPERTY_PLACE_ID, $this->location->get_id(), $publication_alias, true);
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            
            
            $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
            $object_alias = RepositoryDataManager :: get_instance()->get_alias(ContentObject :: get_table_name());
            
            $search_conditions = array();
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', $user_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPublication :: PROPERTY_NAME, '*' . $query . '*', $publication_alias, true);
            $search_conditions[] = new PatternMatchCondition(InternshipOrganizerPublication :: PROPERTY_DESCRIPTION, '*' . $query . '*', $publication_alias, true);
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', $object_alias, true);
            $search_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', $object_alias, true);
            $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function get_mentor_condition()
    {
        
        $mentor_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMentor :: get_table_name());
        $mentor_rel_location_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMentorRelLocation :: get_table_name());
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(InternshipOrganizerMentorRelLocation :: PROPERTY_LOCATION_ID, $this->location->get_id(), $mentor_rel_location_alias, true);
        
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_LOCATIONS)), Translation :: get('ViewInternshipOrganizerOrganisations')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID, self :: PARAM_LOCATION_ID);
    }

}
?>