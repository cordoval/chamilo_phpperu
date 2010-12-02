<?php
namespace common\extensions\external_repository_manager\implementation\flickr;

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
use repository\ExternalSync;
use repository\RepositoryManager;

class FlickrExternalRepositoryManagerImporterComponent extends FlickrExternalRepositoryManager
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
            $image->set_filename($external_object->get_id() . '.jpg');

            $sizes = $external_object->get_available_sizes();
            $image->set_in_memory_file(file_get_contents($external_object->get_url(array_pop($sizes))));

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