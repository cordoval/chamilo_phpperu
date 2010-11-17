<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Application;
use repository\RepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use repository\ContentObject;
use repository\ExternalRepositorySync;
use repository\content_object\soundcloud\Soundcloud;

class SoundcloudExternalRepositoryManagerImporterComponent extends SoundcloudExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($object)
    {
        $soundcloud = ContentObject :: factory(Soundcloud :: get_type_name());
        $soundcloud->set_title($object->get_title());
        $soundcloud->set_description(nl2br($object->get_description()));
        $soundcloud->set_track_id($object->get_id());
        $soundcloud->set_owner_id($this->get_user_id());

        if ($soundcloud->create())
        {
            ExternalRepositorySync :: quicksave($soundcloud, $object, $this->get_external_repository()->get_id());
            $parameters = $this->get_parameters();
            $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
            $this->redirect(Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES), false, $parameters, array(
                    ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY,
                    ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirect(Translation :: get('ObjectFailedImported', null, Utilities :: COMMON_LIBRARIES), true, $parameters);
        }

    }
}
?>