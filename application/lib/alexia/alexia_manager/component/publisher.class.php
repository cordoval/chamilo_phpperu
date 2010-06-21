<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.alexia.alexia_manager.component
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/alexia_publisher.class.php';

class AlexiaManagerPublisherComponent extends AlexiaManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
        $trail->add_help('alexia general');

        $repo_viewer = new RepoViewer($this, Link :: get_type_name());

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new AlexiaPublisher($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }
}
?>