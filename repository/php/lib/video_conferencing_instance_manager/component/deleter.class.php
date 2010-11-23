<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

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
                    $message = 'ObjectNotDeleted';
                    $parameter = array('OBJECT' => Translation :: get('ExternalRepository'));
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                    $parameter = array('OBJECTS' => Translation :: get('ExternalRepositories'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                    $parameter = array('OBJECT' => Translation :: get('ExternalRepository'));
                }
                else
                {
                    $message = 'ObjectsDeleted';
                    $parameter = array('OBJECTS' => Translation :: get('ExternalRepositories'));
                }
            }

            $this->redirect(Translation :: get($message, $parameter, Utilities :: COMMON_LIBRARIES), ($failures ? true : false), array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_BROWSE_INSTANCES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('ExternalRepository')), Utilities :: COMMON_LIBRARIES)));
        }
    }
}
?>