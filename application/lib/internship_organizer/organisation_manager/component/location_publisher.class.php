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
       
        $agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
        $this->agreement = $this->retrieve_agreement($agreement_id);
        $this->set_parameter(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID, $agreement_id);

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);

            $repo_viewer->run();
        }
        else
        {
            $publisher = new InternshipOrganizerMomentPublisher($this);
            $publisher->get_publications_form(RepoViewer::get_selected_objects());
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