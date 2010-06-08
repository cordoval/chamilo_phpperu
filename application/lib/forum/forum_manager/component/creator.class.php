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
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForum')));
        $trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForumPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishForum')));
        
        $pub = new RepoViewer($this, Forum :: get_type_name());
        
        if (!$pub->is_ready_to_be_published())
        {
            
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new ForumPublicationPublisher($pub);
            $html[] = $publisher->publish($pub->get_selected_objects());
        }
        
        $this->display_header($trail);
        
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>