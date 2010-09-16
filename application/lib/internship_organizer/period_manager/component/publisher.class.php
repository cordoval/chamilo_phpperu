<?php

require_once dirname(__FILE__) . '/../../publisher/period_publisher.class.php';

class InternshipOrganizerPeriodManagerPublisherComponent extends InternshipOrganizerPeriodManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $period_id = $_GET[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID];
               
        if ($period_id)
        {
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
            {
                $this->display_header($trail);
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            $type = InternshipOrganizerPeriodPublisher :: SINGLE_PERIOD_TYPE;
            $this->set_parameter(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID, $period_id);
        }
        else
        {
            
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_PUBLISH, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
            {
                $this->display_header($trail);
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $type = InternshipOrganizerPeriodPublisher :: MULTIPLE_PERIOD_TYPE;
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
            $publisher = new InternshipOrganizerPeriodPublisher($this, $type);
            $publisher->get_publications_form(RepoViewer :: get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name());
    }
}
?>