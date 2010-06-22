<?php

class InternshipOrganizerPeriodManagerPublisherComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS)), Translation :: get('BrowsePeriods')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
        $trail->add_help('internship organizer general');

        $repo_viewer = new RepoViewer($this, array(Document :: get_type_name()));

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
//            $publisher = new PersonalCalendarPublisher($this);
//            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }
}
?>