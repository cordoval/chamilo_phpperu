<?php
/**
 * $Id: forum_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum.component
 */
require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';

class ForumToolViewerComponent extends ForumToolComponent
{
	private $trail;
	private $root_content_object;
	
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $cid = Request :: get(Tool :: PARAM_COMPLEX_ID);
        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        Request :: set_get('pid', $pid);
        
        $this->set_parameter(Tool :: PARAM_ACTION, ForumTool :: ACTION_VIEW_FORUM);
        $this->trail = $trail = new BreadcrumbTrail();
       // $this->display_header(new BreadcrumbTrail());
        
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);
        
   		$object = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
   		$this->root_content_object = $object->get_content_object();
        
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

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $parameters[Tool :: PARAM_ACTION] = ForumTool :: ACTION_VIEW_FORUM;
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
    {
        $parameters[Tool :: PARAM_ACTION] = ForumTool :: ACTION_VIEW_FORUM;
        $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
    }
}
?>