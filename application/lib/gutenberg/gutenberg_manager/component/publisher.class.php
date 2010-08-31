<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.gutenberg.gutenberg_manager.component
 */
require_once dirname(__FILE__) . '/../gutenberg_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/gutenberg_publisher.class.php';

class GutenbergManagerPublisherComponent extends GutenbergManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GutenbergManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Gutenberg')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
        $trail->add_help('gutenberg general');

        $repo_viewer = RepoViewer :: construct($this);

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new GutenbergPublisher($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(ComicBook :: get_type_name());
    }
}
?>