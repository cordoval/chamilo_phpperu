<?php
/**
 * $Id: viewer.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
require_once dirname(__FILE__) . '/../forum_manager.class.php';
require_once dirname(__FILE__) . '/../forum_manager_component.class.php';

/**
 * Component to view a new forum_publication object
 * @author Michael Kyndt
 */
class ForumManagerViewerComponent extends ForumManager
{
	private $trail;
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->trail = $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForum')));
        
        $pid = Request :: get('pid');
        $trail->add(new Breadcrumb($this->get_url(array('display_action' => 'view_forum', 'pid' => $pid)), Translation :: get('ViewForum')));
        
        $cid = Request :: get('cid');
        
        //$this->display_header($trail);
        
        $cd = ComplexDisplay :: factory($this, Forum :: get_type_name());
        $cd->run();
        
        //$this->display_footer();
        
        switch ($cd->get_action())
        {
            case ForumDisplay :: ACTION_VIEW_TOPIC :
                Events :: trigger_event('view_forum_topic', 'weblcms', array('user_id' => $this->get_user_id(), 'publication_id' => $pid, 'forum_topic_id' => $cid));
                break;
        }
    }
    
    function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}
    	
    	return parent :: display_header($this->trail);
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $parameters[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_VIEW;
        return parent :: get_url($parameters, $filter, $encode_entities);
    }

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
    {
        $parameters[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_VIEW;
        parent :: redirect($message, $error_message, $parameters, $filter, $encode_entities);
    }
}
?>