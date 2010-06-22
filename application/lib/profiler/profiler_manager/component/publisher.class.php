<?php
/**
 * $Id: publisher.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/profile_publisher.class.php';

class ProfilerManagerPublisherComponent extends ProfilerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishProfile')));
        $trail->add_help('profiler general');

        $repo_viewer = new RepoViewer($this, Profile :: get_type_name());

        if (!$repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new ProfilePublisher($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }
}
?>