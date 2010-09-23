<?php

require_once dirname(__FILE__) . '/../../publisher/location_publisher.class.php';

class InternshipOrganizerOrganisationManagerPublisherComponent extends InternshipOrganizerOrganisationManager implements RepoViewerInterface
{

    private $type;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $location_id = $_GET[self :: PARAM_LOCATION_ID];
        
        if ($location_id)
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $location_id, InternshipOrganizerRights :: TYPE_))
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $this->type = InternshipOrganizerAgreementPublisher :: SINGLE_AGREEMENT_TYPE;
            $this->set_parameter(self :: PARAM_AGREEMENT_ID, $location_id);
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
            $this->type = InternshipOrganizerAgreementPublisher :: MULTIPLE_AGREEMENT_TYPE;
        }
             
        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new InternshipOrganizerAgreementPublisher($this, $this->type);
            $publisher->get_publications_form(RepoViewer :: get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name());
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerAgreements')));
    	$location_id = Request :: get(self :: PARAM_LOCATION_ID);
        if($location_id){
    		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOCATION, self :: PARAM_LOCATION_ID => $location_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerLocationViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerLocation')));
    	}   
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_LOCATION_ID);
    }

}
?>