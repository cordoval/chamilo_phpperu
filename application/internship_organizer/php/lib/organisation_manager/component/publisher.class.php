<?php

require_once dirname(__FILE__) . '/../../publisher/location_publisher.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/organisation_manager/component/browser.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/organisation_manager/component/viewer.class.php';


class InternshipOrganizerOrganisationManagerPublisherComponent extends InternshipOrganizerOrganisationManager implements RepoViewerInterface
{

    private $type;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $location_id = Request :: get(self :: PARAM_LOCATION_ID);
        $organisation_id = Request :: get(self :: PARAM_ORGANISATION_ID);
        
        if ($location_id)
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $location_id, InternshipOrganizerRights :: TYPE_LOCATION))
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $this->type = InternshipOrganizerLocationPublisher :: SINGLE_LOCATION_TYPE;
            $this->set_parameter(self :: PARAM_LOCATION_ID, $location_id);
        }
        else
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $this->type = InternshipOrganizerLocationPublisher :: MULTIPLE_LOCATION_TYPE;
            $this->set_parameter(self :: PARAM_ORGANISATION_ID, $organisation_id);
        }
             
        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new InternshipOrganizerLocationPublisher($this, $this->type);
            $publisher->get_publications_form(RepoViewer :: get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name());
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
        
        $location_id = Request :: get(self :: PARAM_LOCATION_ID);
        if($location_id){
    		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOCATION, self :: PARAM_LOCATION_ID => $location_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerLocationViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerLocation')));
    	}   
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID, self :: PARAM_LOCATION_ID);
    }

}
?>