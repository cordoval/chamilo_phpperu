<?php
class ExternalRepositoryInstanceManagerDeleterComponent extends ExternalRepositoryInstanceManager
{

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }
        
        $ids = Request :: get(ExternalRepositoryInstanceManager :: PARAM_INSTANCE);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $external_repository = $this->retrieve_external_repository($id);
                
                if (! $external_repository->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedExternalRepositoryDeleted';
                }
                else
                {
                    $message = 'SelectedExternalRepositoryDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedExternalRepositoriesDeleted';
                }
                else
                {
                    $message = 'SelectedExternalRepositoriesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_BROWSE_INSTANCES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoExternalRepositorySelected')));
        }
    }
}
?>