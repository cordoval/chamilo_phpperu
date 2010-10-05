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
class ForumManagerCreatorComponent extends ForumManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new ForumPublicationPublisher($this);
            $publisher->publish(RepoViewer :: get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Forum :: get_type_name());
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_publisher');
    }

    function get_additional_parameters()
    {
    	return array(RepoViewer :: PARAM_ACTION, RepoViewer :: PARAM_ID);
    }
}

?>