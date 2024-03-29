<?php

namespace application\forum;

use common\libraries\Request;
use repository\RepositoryDataManager;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

class ForumManagerChangeLockComponent extends ForumManager
{

    function run()
    {
        $forum_publication = $this->retrieve_forum_publication(Request :: get(ForumManager :: PARAM_PUBLICATION_ID));
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($forum_publication->get_forum_id());
        if($object->invert_locked())
        {
        	$succes = true;
        	$message = Translation :: get('LockChanged', null, 'repository\content_object\forum');
        }
        else
        {
        	$message= Translation :: get('LockNotChanged', null, 'repository\content_object\forum');
        }
        
        $params = array();
        $params[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_BROWSE;
        
        $this->redirect($message, !$succes, $params);
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_category_manager');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PUBLICATION_ID);
    }
}

?>