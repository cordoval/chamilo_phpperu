<?php
/**
 * $Id: forum_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum.component
 */
require_once dirname(__FILE__) . '/../forum_tool.class.php';

class ForumToolViewerComponent extends ForumTool
{
	private $trail;
	private $root_content_object;
	private $publication_id;
	
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $this->publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $this->publication_id);
        
        $this->trail = $trail = BreadcrumbTrail :: get_instance();
        
   		$object = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
   		$this->root_content_object = $object->get_content_object();
        
        $cd = ComplexDisplay :: factory($this, Forum :: get_type_name());
        $cd->run();

    }
    
    function get_root_content_object()
    {
    	return $this->root_content_object;
    }
    
	function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}
    	
    	return parent :: display_header($this->trail);
    }
    
	function topic_viewed($complex_topic_id)
    {
        require_once dirname(__FILE__) . '/../../../trackers/weblcms_forum_topic_views_tracker.class.php';
        
    	$parameters = array();
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_USER_ID] = $this->get_user_id();
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_FORUM_TOPIC_ID] = $complex_topic_id;
        
        Event :: trigger('view_forum_topic', WeblcmsManager :: APPLICATION_NAME, $parameters);
    }
    
	function count_topic_views($complex_topic_id)
    {
    	require_once dirname(__FILE__) . '/../../../trackers/weblcms_forum_topic_views_tracker.class.php';
    	 
    	$conditions[] = new EqualityCondition(WeblcmsForumTopicViewsTracker :: PROPERTY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(WeblcmsForumTopicViewsTracker :: PROPERTY_FORUM_TOPIC_ID, $complex_topic_id);
        $condition = new AndCondition($conditions);
        
        $dummy = new WeblcmsForumTopicViewsTracker();
        return $dummy->count_tracker_items($condition);
    }
}
?>