<?php
class ExternalRepositoryComponentInternalSyncerComponent extends ExternalRepositoryComponent
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        
        if ($id)
        {
            $object = $this->retrieve_external_repository_object($id);
            
            if (! $object->is_importable() && ($object->get_synchronization_status() == ExternalRepositorySync :: SYNC_STATUS_INTERNAL || $object->get_synchronization_status() == ExternalRepositorySync :: SYNC_STATUS_CONFLICT))
            {
                $succes = $this->synchronize_internal_repository_object($object);
                
                $params = $this->get_parameters();
                $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = '';
                
                if ($succes)
                {
                    $this->redirect(Translation :: get('Succes'), false, $params);
                }
                else
                {
                    $this->redirect(Translation :: get('Failed'), true, $params);
                }
            }
        }
        else
        {
            $params = $this->get_parameters();
            $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $this->redirect(null, false, $params);
        }
    }
}
?>