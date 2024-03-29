<?php

namespace application\forum;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
/**
 * $Id: toggle_visibility.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
class ForumManagerToggleVisibilityComponent extends ForumManager
{

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            if (Request :: get(ForumManager :: PARAM_PUBLICATION_ID))
            {
                $publication_ids = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
            }
            else
            {
                $publication_ids = $_POST[ForumManager :: PARAM_PUBLICATION_ID];
            }
            
            if (! is_array($publication_ids))
            {
                $publication_ids = array($publication_ids);
            }
            
            $datamanager = ForumDataManager :: get_instance();
            
            foreach ($publication_ids as $index => $pid)
            {
                $publication = $datamanager->retrieve_forum_publication($pid);
                
                if (Request :: get(PARAM_VISIBILITY))
                {
                    $publication->set_hidden(Request :: get(PARAM_VISIBILITY));
                }
                else
                {
                    $publication->toggle_visibility();
                }
                
                $publication->update();
            }
            
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ForumsVisibilityChanged'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ForumVisibilityChanged'));
            }
            
            $params = array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE);
            //			if(Request :: get('details') == 1)
            //			{
            //				$params['pid'] = $pid;
            //				$params['tool_action'] = 'view';
            //			}
            

            $this->redirect($message, '', $params);
            
            $this->redirect($message, false, $params);
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_toggle_visibility');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>