<?php
namespace common\extensions\external_repository_manager\implementation\photobucket;

use common\libraries\StringUtilities;
use common\libraries\Translation;
use common\libraries\PlatformSetting;
use common\libraries\Application;
use common\libraries\Utilities;

use repository\ContentObject;
use repository\RepositoryManager;
use repository\ExternalSync;
use repository\content_object\document\Document;

use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class PhotobucketExternalRepositoryManagerImporterComponent extends PhotobucketExternalRepositoryManager
{

	function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($external_object)
    {
        if ($external_object->is_importable())
        {
            $image = ContentObject :: factory(Document :: get_type_name());
            $image->set_title($external_object->get_title());

            if (PlatformSetting :: get('description_required', 'repository') && StringUtilities :: is_null_or_empty($external_object->get_description()))
            {
                $image->set_description('-');
            }
            else
            {
                $image->set_description($external_object->get_description());
            }

            $image->set_owner_id($this->get_user_id());
            $id = explode('/', urldecode($external_object->get_id()));
            $id = $id[(count($id) - 1)];

            $image->set_filename($id);

//            $sizes = $external_object->get_available_sizes();
            $image->set_in_memory_file(file_get_contents($external_object->get_url()));

            if ($image->create())
            {
                ExternalSync :: quicksave($image, $external_object, $this->get_external_repository()->get_id());

                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                $this->redirect(Translation :: get('ObjectImported', null, Utilities :: COMMON_LIBRARIES), false, $parameters, array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirect(Translation :: get('ObjectFailedImported', null, Utilities :: COMMON_LIBRARIES), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(null, false, $parameters);
        }

    }
}
?>