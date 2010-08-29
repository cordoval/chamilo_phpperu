<?php
/**
 * $Id: viewer.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
require_once dirname(__FILE__) . '/../forum_manager.class.php';

/**
 * Component to view a new forum_publication object
 * @author Michael Kyndt
 */
class ForumManagerViewerComponent extends ForumManager
{
    private $trail;
    private $publication_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->trail = $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(parent :: get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForum')));
        
        $this->publication_id = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
        $this->set_parameter(ForumManager :: PARAM_PUBLICATION_ID, $this->publication_id);
        
        ComplexDisplay :: launch(Forum :: get_type_name(), $this);
    }

    function display_header($trail)
    {
        return parent :: display_header($this->trail);
    }

    function get_root_content_object()
    {
        $datamanager = ForumDataManager :: get_instance();
        $pub = $datamanager->retrieve_forum_publication($this->publication_id);
        $forum_id = $pub->get_forum_id();
        return RepositoryDataManager :: get_instance()->retrieve_content_object($forum_id);
    }

    function topic_viewed($complex_topic_id)
    {
        $parameters = array();
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_USER_ID] = $this->get_user_id();
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_FORUM_TOPIC_ID] = $complex_topic_id;
        
        Event :: trigger('view_forum_topic', ForumManager :: APPLICATION_NAME, $parameters);
    }

    function count_topic_views($complex_topic_id)
    {
        require_once dirname(__FILE__) . '/../../trackers/forum_topic_view_tracker.class.php';
        
        $conditions[] = new EqualityCondition(ForumTopicViewTracker :: PROPERTY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(ForumTopicViewTracker :: PROPERTY_FORUM_TOPIC_ID, $complex_topic_id);
        $condition = new AndCondition($conditions);
        
        $dummy = new ForumTopicViewTracker();
        return $dummy->count_tracker_items($condition);
    }

}
?>