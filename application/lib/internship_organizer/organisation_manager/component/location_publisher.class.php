<?php

require_once dirname(__FILE__) . '/../../publisher/moment_publisher.class.php';

class InternshipOrganizerOrganisationManagerLocationPublisherComponent extends InternshipOrganizerOrganisationManager implements RepoViewerInterface
{

    private $agreement;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
        $this->agreement = $this->retrieve_agreement($agreement_id);
        $this->set_parameter(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID, $agreement_id);

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $this->agreement->get_name()));

        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
        $trail->add_help('internship organizer general');

        $repo_viewer = RepoViewer :: construct($this);

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new InternshipOrganizerMomentPublisher($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }

    function get_agreement()
    {
        return $this->agreement;
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name());
    }

}
?>