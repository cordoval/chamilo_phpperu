<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Utilities;

use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;

use repository\RepositoryManager;
use repository\ContentObject;
use repository\ExternalRepositorySync;
use repository\content_object\youtube\Youtube;

class YoutubeExternalRepositoryManagerImporterComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($object)
    {
        $youtube = ContentObject :: factory(Youtube :: get_type_name());
        $youtube->set_title($object->get_title());
        $youtube->set_description($object->get_description());
        $youtube->set_url('http://www.youtube.com/watch?v=' . $object->get_id());
        $youtube->set_height(344);
        $youtube->set_width(425);
        $youtube->set_owner_id($this->get_user_id());
        
        if ($youtube->create())
        {
            ExternalRepositorySync :: quicksave($youtube, $object, $this->get_external_repository()->get_id());
            $parameters = $this->get_parameters();
            $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
            $this->redirect(Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES), false, $parameters, array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
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