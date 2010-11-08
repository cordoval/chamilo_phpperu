<?php
namespace common\extensions\external_repository_manager\implementation\photobucket;

use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryManager;


class PhotobucketExternalRepositoryManagerExporterComponent extends PhotobucketExternalRepositoryManager
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
            $this->redirect(Translation :: get('ObjectExported', null, Utilities :: COMMON_LIBRARIES), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_EXPORT_EXTERNAL_REPOSITORY;
            $this->redirect(Translation :: get('ObjectFailedExported', null, Utilities :: COMMON_LIBRARIES), true, $parameters);
        }
    }

}
?>