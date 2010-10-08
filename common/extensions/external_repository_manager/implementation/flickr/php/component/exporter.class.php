<?php
class FlickrExternalRepositoryManagerExporterComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function export_external_repository_object($object)
    {
        $success = parent :: export_external_repository_object($object);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(Translation :: get('ExportSuccesfull'), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_EXPORT_EXTERNAL_REPOSITORY;
            $this->redirect(Translation :: get('ExportFailed'), true, $parameters);
        }
    }

}
?>