<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use repository\ContentObject;
use repository\ExternalSync;
use common\libraries\Application;
use common\libraries\Translation;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\content_object\mediamosa\Mediamosa;
use repository\RepositoryManager;
use common\libraries\Utilities;

/**
 * Description of importerclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerImporterComponent extends MediamosaExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($object)
    {
        if ($object->is_importable())
        {

            $streaming_video_clip = ContentObject :: factory(Mediamosa :: get_type_name());
            $streaming_video_clip->set_title($object->get_title());
            $streaming_video_clip->set_description($object->get_description());
            $streaming_video_clip->set_owner_id($this->get_user_id());

            if ($streaming_video_clip->create())
            {
                ExternalSync :: quicksave($streaming_video_clip, $object, $this->get_external()->get_id());
                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                $this->redirect(Translation :: get('Succes', null, Utilities :: COMMON_LIBRARIES), false, $parameters, array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
                $this->redirect(Translation :: get('Failed', null, Utilities :: COMMON_LIBRARIES), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirect(null, false, $parameters);
        }
    }

}
?>