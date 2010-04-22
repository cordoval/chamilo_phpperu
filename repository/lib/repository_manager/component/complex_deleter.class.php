<?php
/**
 * $Id: complex_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerComplexDeleterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_CLOI_ID);
        $root = Request :: get(RepositoryManager :: PARAM_CLOI_ROOT_ID);
        $failures = 0;
        $parent = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $cloi_id)
            {
                $cloi = $this->retrieve_complex_content_object_item($cloi_id);
                if ($parent == 0)
                    $parent = $cloi->get_parent();
                
                if ($cloi->get_user_id() == $this->get_user_id())
                {
                    // TODO: check if deletion is allowed
                    //if ($this->get_parent()->complex_content_object_item_deletion_allowed($cloi))
                    {
                        if (! $cloi->delete())
                        {
                            $failures ++;
                        }
                    }
                }
                else
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedObjectNotDeleted';
                }
                else
                {
                    $message = 'NotAllSelectedObjectsDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedObjectDeleted';
                }
                else
                {
                    $message = 'AllSelectedObjectsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $parent, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root, 'publish' => Request :: get('publish'), 'clo_action' => 'organise'));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>