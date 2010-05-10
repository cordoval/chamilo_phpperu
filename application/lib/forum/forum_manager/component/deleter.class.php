<?php
/**
 * $Id: deleter.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
require_once dirname(__FILE__) . '/../forum_manager.class.php';

/**
 * Component to delete forum_publications objects
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumManagerDeleterComponent extends ForumManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ForumManager :: PARAM_FORUM_PUBLICATION);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $forum_publication = $this->retrieve_forum_publication($id);
            	if(WebApplication :: is_active('gradebook'))
       			{
       				require_once dirname(__FILE__) . '/../../../gradebook/gradebook_utilities.class.php';
			    	if(!GradebookUtilities :: move_internal_item_to_external_item(WikiManager :: APPLICATION_NAME, $forum_publication->get_id()))
			    		$message = 'failed to move internal evaluation to external evaluation';
       			}
                if (! $forum_publication->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedForumPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedForumPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedForumPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedForumPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoForumPublicationsSelected')));
        }
    }
}
?>