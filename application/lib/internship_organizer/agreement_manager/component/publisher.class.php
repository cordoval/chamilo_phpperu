<?php

require_once dirname(__FILE__) . '/../../publisher/agreement_publisher.class.php';

class InternshipOrganizerAgreementManagerPublisherComponent extends InternshipOrganizerAgreementManager implements RepoViewerInterface
{

    private $type;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = $_GET[self :: PARAM_AGREEMENT_ID];
        
        if ($agreement_id)
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
            {
                $this->display_header($trail);
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $this->type = InternshipOrganizerAgreementPublisher :: SINGLE_AGREEMENT_TYPE;
            $this->set_parameter(self :: PARAM_AGREEMENT_ID, $agreement_id);
        }
        else
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
            {
                $this->display_header($trail);
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $this->type = InternshipOrganizerAgreementPublisher :: MULTIPLE_AGREEMENT_TYPE;
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('internship organizer general');
        
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
    	$agreement_id = Request :: get(self :: PARAM_AGREEMENT_ID);
        if($agreement_id){
    		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_PUBLICATIONS)), Translation :: get('ViewInternshipOrganizerAgreement')));
    	}   
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID);
    }

}
?>