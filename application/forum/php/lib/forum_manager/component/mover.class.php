<?php

namespace application\forum;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;

/**
 * $Id: mover.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */

class ForumManagerMoverComponent extends ForumManager
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $move = 0;
            $fpid = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
            if (Request :: get(ForumManager :: PARAM_MOVE))
            {
                $move = Request :: get(ForumManager :: PARAM_MOVE);
            }
            
            $datamanager = ForumDataManager :: get_instance();
            $publication = $datamanager->retrieve_forum_publication($fpid);
            if ($publication->move($move))
            {
                $message = Translation :: get('ObjectMoved' , array('OBJECT' => Translation :: get('Forum', null, 'repository\content_object\forum')) , Utilities :: COMMON_LIBRARIES);
            }
            else
            {
       			$message = Translation :: get('ObjectNotMoved' , array('OBJECT' => Translation :: get('Forum', null, 'repository\content_object\forum')) , Utilities :: COMMON_LIBRARIES);
            }
            
            $this->redirect($message, false, array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_mover');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_MOVE);
    }
}
?>