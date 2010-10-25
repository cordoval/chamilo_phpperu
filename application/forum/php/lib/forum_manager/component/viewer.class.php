<?phpnamespace application\forum
/**
 * $Id: viewer.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
require_once WebApplication :: get_application_class_path('forum') . 'trackers/forum_topic_view_tracker.class.php';

/**
 * Component to view a new forum_publication object
 * @author Michael Kyndt
 */
class ForumManagerViewerComponent extends ForumManager implements DelegateComponent
{
    private $trail;
    private $publication_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->publication_id = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
        
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
        $parameters[ForumTopicViewTracker :: PROPERTY_USER_ID] = $this->get_user_id();
        $parameters[ForumTopicViewTracker :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
        $parameters[ForumTopicViewTracker :: PROPERTY_FORUM_TOPIC_ID] = $complex_topic_id;
        
        Event :: trigger('view_forum_topic', ForumManager :: APPLICATION_NAME, $parameters);
    }

    function count_topic_views($complex_topic_id)
    {
        $conditions[] = new EqualityCondition(ForumTopicViewTracker :: PROPERTY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(ForumTopicViewTracker :: PROPERTY_FORUM_TOPIC_ID, $complex_topic_id);
        $condition = new AndCondition($conditions);
        
        $dummy = new ForumTopicViewTracker();
        return $dummy->count_tracker_items($condition);
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PUBLICATION_ID);
    }

}
?>