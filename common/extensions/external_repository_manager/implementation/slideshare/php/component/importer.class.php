<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\libraries\Redirect;
use common\libraries\PlatformSetting;
use common\libraries\StringUtilities;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Utilities;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

use repository\ContentObject;
use repository\content_object\slideshare\Slideshare;
use repository\ExternalSync;
use repository\RepositoryManager;

class SlideshareExternalRepositoryManagerImporterComponent extends SlideshareExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($object)
    {
        $slideshow = ContentObject :: factory(Slideshare :: get_type_name());        
        $slideshow->set_title($object->get_title());        
        $slideshow->set_description($object->get_description());
        $slideshow->set_owner_id($this->get_user_id());  
        
        if ($slideshow->create())
        {
            ExternalSync :: quicksave($slideshow, $object, $this->get_external_repository()->get_id());
            
            $parameters = $this->get_parameters();
            $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
            $this->redirect(Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES), false, $parameters, array(
            ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
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