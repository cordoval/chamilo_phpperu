<?php
/**
 * $Id: user_view_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerUserViewDeleterComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_USER_VIEW);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $user_view_id)
            {
                $uv = new UserView();
                $uv->set_id($user_view_id);
                
                if (! $uv->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedUserViewNotDeleted';
                }
                else
                {
                    $message = 'NotAllSelectedUserViewsDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedUserViewDeleted';
                }
                else
                {
                    $message = 'AllSelectedUserViewsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), $failures ? true : false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoUserViewSelected')));
        }
    }
}
?>