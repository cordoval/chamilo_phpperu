<?php
namespace common\extensions\external_repository_manager\implementation\hq23;

use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;

use common\libraries\Translation;
use common\libraries\StringUtilities;
use common\libraries\PlatformSetting;

class Hq23ExternalRepositoryManagerInternalSyncerComponent extends Hq23ExternalRepositoryManager
{

    function run()
    {
        $syncer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: INTERNAL_SYNCER_COMPONENT, $this);
        $syncer->run();
    }

    function synchronize_internal_repository_object(ExternalRepositoryObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $content_object = $synchronization_data->get_content_object();
        
        if (StringUtilities :: is_null_or_empty($external_object->get_title()))
        {
            $content_object->set_title($external_object->get_id() . '.jpg');
        }
        else
        {
			$content_object->set_title($external_object->get_title());        
        }
        
        if (PlatformSetting :: get('description_required', 'repository') && StringUtilities :: is_null_or_empty($external_object->get_description()))
        {
            $content_object->set_description('-');
        }
        else
        {
            $content_object->set_description($external_object->get_description());
        }
        
        if ($content_object->update())
        {
            $synchronization_data->set_content_object_timestamp($content_object->get_modification_date());
            $synchronization_data->set_external_repository_object_timestamp($external_object->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = $content_object->get_id();
                $this->redirect(Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES), false, $parameters, array(
                ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirect(Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES), true, $parameters);
        }
    }
}
?>