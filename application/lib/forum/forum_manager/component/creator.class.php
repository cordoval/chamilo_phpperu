<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.forum.forum_manager.component
 */
require_once dirname(__FILE__) . '/../forum_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/forum_publication_publisher.class.php';
require_once dirname(__FILE__) . '/../../forms/forum_publication_form.class.php';

/**
 * Component to create a new forum_publication object
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumManagerCreatorComponent extends ForumManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForum')));
        //$trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForumPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishForum')));

        $repo_viewer = new RepoViewer($this, Forum :: get_type_name());

        if (!$repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new ForumPublicationPublisher($this);
            $publisher->publish($repo_viewer->get_selected_objects());
        }
    }
}
?>