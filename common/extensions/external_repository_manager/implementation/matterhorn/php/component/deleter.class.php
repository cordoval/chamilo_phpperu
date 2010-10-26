<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;

use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

class MatterhornExternalRepositoryManagerDeleterComponent extends MatterhornExternalRepositoryManager
{

 	function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function delete_external_repository_object($id)
    {
        $success = parent :: delete_external_repository_object($id);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(Translation :: get('DeleteSuccesfull'), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $this->redirect(Translation :: get('DeleteFailed'), true, $parameters);
        }
    }
}
?>