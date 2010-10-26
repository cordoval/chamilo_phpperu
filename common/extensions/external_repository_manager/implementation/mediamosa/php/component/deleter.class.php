<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\libraries\Translation;
/**
 * Description of deleterclass
 *
 * @author jevdheyd
 */

class MediamosaExternalRepositoryManagerDeleterComponent extends MediamosaExternalRepositoryManager
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
            $this->redirect(Translation :: get('Success'), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $this->redirect(Translation :: get('Failed'), true, $parameters);
        }
    }
}
?>
