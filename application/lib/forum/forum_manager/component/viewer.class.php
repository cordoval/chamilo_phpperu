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
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->trail = $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(parent :: get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('BrowseForum')));
        
        $publication_id = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
        $this->set_parameter(ForumManager :: PARAM_PUBLICATION_ID, $publication_id);
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewForum')));
        
        $cd = ComplexDisplay :: factory($this, Forum :: get_type_name());
        $cd->run();
        
        switch ($cd->get_action())
        {
            case ForumDisplay :: ACTION_VIEW_TOPIC :
                Events :: trigger_event('view_forum_topic', 'weblcms', array('user_id' => $this->get_user_id(), 'publication_id' => $publication_id, 'forum_topic_id' => $cd->get_complex_content_object_item_id()));
                break;
        }
    }
    
    function display_header($trail)
    {
       	if($trail)
    	{
    		$trail->remove(0);
    		$this->trail->merge($trail);
    	}
    	
    	return parent :: display_header($this->trail);
    }

    function get_root_content_object()
    {
    	$datamanager = ForumDataManager :: get_instance();
    	$publication_id = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
        $pub = $datamanager->retrieve_forum_publication($publication_id);
    	$forum_id = $pub->get_forum_id();
       	return RepositoryDataManager :: get_instance()->retrieve_content_object($forum_id);
    }

}
?>