<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;

class ExternalRepositoryInstanceManagerDeactivatorComponent extends ExternalRepositoryInstanceManager
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
                $external_repository->deactivate();

                if (! $external_repository->update())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotDeactivated';
                    $parameter = array('OBJECT' => Translation :: get('ExternalRepository'));
                }
                else
                {
                    $message = 'ObjectsNotDeactivated';
                    $parameter = array('OBJECTS' => Translation :: get('ExternalRepositories'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeactivated';
                    $parameter = array('OBJECT' => Translation :: get('ExternalRepository'));
                }
                else
                {
                    $message = 'ObjectsDeactivated';
                    $parameter = array('OBJECTS' => Translation :: get('ExternalRepositories'));
                }
            }

            $this->redirect(Translation :: get($message, $parameter, Utilities :: COMMON_LIBRARIES), ($failures ? true : false), array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_BROWSE_INSTANCES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoExternalRepositorySelected')));
        }
    }
}
?>