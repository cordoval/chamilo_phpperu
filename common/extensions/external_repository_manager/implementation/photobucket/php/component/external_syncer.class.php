<?php
namespace common\extensions\external_repository_manager\implementation\photobucket;

use common\libraries\Translation;
use common\libraries\Utilities;

use repository\RepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryObject;

class PhotobucketExternalRepositoryManagerExternalSyncerComponent extends PhotobucketExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function synchronize_external_repository_object(ExternalRepositoryObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $content_object = $synchronization_data->get_content_object();

        $values = array();
        $values[ExternalRepositoryObject :: PROPERTY_ID] = $external_object->get_id();
        $values[ExternalRepositoryObject :: PROPERTY_TITLE] = trim(html_entity_decode(strip_tags($content_object->get_title())));
        $values[ExternalRepositoryObject :: PROPERTY_DESCRIPTION] = trim(html_entity_decode(strip_tags($content_object->get_description())));
        $values[PhotobucketExternalRepositoryObject :: PROPERTY_TAGS] = $external_object->get_tags_string(false);

        if ($this->get_external_repository_connector()->update_external_repository_object($values))
        {
            $external_object = $this->get_external_repository_connector()->retrieve_external_repository_object($external_object->get_id());

            $synchronization_data->set_content_object_timestamp($content_object->get_modification_date());
            $synchronization_data->set_external_object_timestamp($external_object->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = $content_object->get_id();
                $this->redirect(Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES), false, $parameters, array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirect(Translation :: get('ObjectFailedUpdated', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(Translation :: get(get('ObjectFailedUpdated', array('OBJECT' => Translation :: get('ExternalRepository')), Utilities :: COMMON_LIBRARIES)), true, $parameters);
        }
    }
}
?>