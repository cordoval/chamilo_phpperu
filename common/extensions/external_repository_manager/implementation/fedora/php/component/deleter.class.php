<?php
namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;

/**
 * Delete - purge - an object from Fedora.
 *
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerDeleterComponent extends FedoraExternalRepositoryManager
{

    function run()
    {
        if ($api = $this->create_api_component())
        {
            return $api->run();
        }

        ExternalRepositoryComponent :: launch($this);
    }

    function delete_external_repository_object($id)
    {
        $success = parent :: delete_external_repository_object($id);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[FedoraExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = FedoraExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(Translation :: get('DeleteSuccesfull'), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[FedoraExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = FedoraExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $parameters[FedoraExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $this->redirect(Translation :: get('DeleteFailed'), true, $parameters);
        }
    }
}
?>