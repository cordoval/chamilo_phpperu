<?php
namespace common\extensions\external_repository_manager\implementation\vimeo;

use common\libraries\Redirect;
use common\libraries\PlatformSetting;
use common\libraries\StringUtilities;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Utilities;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

use repository\ContentObject;
use repository\content_object\document\Document;
use repository\content_object\vimeo\Vimeo;
use repository\ExternalSync;
use repository\RepositoryManager;

class VimeoExternalRepositoryManagerImporterComponent extends VimeoExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($object)
    {
        $vimeo = ContentObject :: factory(Vimeo :: get_type_name());
        $vimeo->set_title($object->get_title());
        $vimeo->set_description($object->get_description());
        $vimeo->set_owner_id($this->get_user_id());
        
        if ($vimeo->create())
        {
            ExternalSync :: quicksave($vimeo, $object, $this->get_external_repository()->get_id());
            
            $parameters = $this->get_parameters();
            $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
            $this->redirect(Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES), false, $parameters, array(
            ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(Translation :: get('ObjectFailedImported', null, Utilities :: COMMON_LIBRARIES), true, $parameters);
        }
    }
}
?>