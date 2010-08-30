<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../../../phrases_manager.class.php';
require_once dirname(__FILE__) . '/../../../../publisher/phrases_publisher.class.php';

class PhrasesPublicationManagerPublisherComponent extends PhrasesPublicationManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_PHRASES)), Translation :: get('Phrases')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
        $trail->add_help('phrases general');

        $repo_viewer = RepoViewer :: construct($this);

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new PhrasesPublisher($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Assessment :: get_type_name());
    }
}
?>