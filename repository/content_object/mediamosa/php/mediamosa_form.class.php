<?php
namespace repository\content_object\mediamosa;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Application;
use repository\ContentObjectForm;
use common\libraries\ExternalRepositoryLauncher;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\ExternalRepositorySync;
use common\extensions\external_repository_manager\ExternalRepositoryObject;
use repository\RepositoryDataManager;
use common\libraries\Utilities;

/**
 * Description of MediamosaForm class
 *
 * @author jevdheyd
 */

require_once Path :: get_common_extensions_path() . 'external_repository_manager/implementation/mediamosa/php/mediamosa_external_repository_object.class.php';

class MediamosaForm extends ContentObjectForm
{

    function build_creation_form()
    {
        parent :: build_creation_form();

        $this->addElement('hidden', ExternalRepositoryObject :: PROPERTY_EXTERNAL_REPOSITORY_ID);
        $this->addElement('hidden', ExternalRepositoryObject :: PROPERTY_ID);

        $external_repositories = ExternalRepositoryLauncher :: get_links(Utilities :: get_classname_from_namespace(ExternalRepositoryManager :: CLASS_NAME, true), Mediamosa :: get_type_name(), true);
        if ($external_repositories)
        {
            $this->addElement('category', Translation :: get('Media'));
            $this->addElement('static', null, null, $external_repositories);
            $this->addElement('category');
        }

    //$this->streaming_video_clip_form_elements();
    }

    function build_editing_form()
    {
        parent :: build_editing_form();

    //$this->streaming_video_clip_form_elements();
    }

    function create_content_object()
    {
        if (parent :: create_content_object())
        {
            $mediamosa = $this->get_content_object();

            $external_respository_sync = new ExternalRepositorySync();
            $external_respository_sync->set_external_repository_id($this->exportValue(ExternalRepositoryObject :: PROPERTY_EXTERNAL_REPOSITORY_ID));
            $external_respository_sync->set_external_repository_object_id($this->exportValue(ExternalRepositoryObject :: PROPERTY_ID));

            $object = $external_respository_sync->get_external_repository_object();

            ExternalRepositorySync :: quicksave($mediamosa, $object, $this->exportValue(ExternalRepositoryObject :: PROPERTY_EXTERNAL_REPOSITORY_ID));

            return $mediamosa;
        }
    }
}
?>