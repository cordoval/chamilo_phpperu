<?php
namespace repository\content_object\matterhorn;

use repository\ExternalSync;

use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\ExternalRepositoryLauncher;

use common\extensions\external_repository_manager\ExternalRepositoryManager;

use repository\ContentObjectForm;

require_once dirname(__FILE__) . '/matterhorn.class.php';

class MatterhornForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $external_repositories = ExternalRepositoryLauncher :: get_links(Utilities :: get_classname_from_namespace(ExternalRepositoryManager :: CLASS_NAME, true), Matterhorn :: get_type_name(), true);
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->addElement('hidden', ExternalSync :: PROPERTY_EXTERNAL_ID);
        $this->addElement('hidden', ExternalSync :: PROPERTY_EXTERNAL_OBJECT_ID);
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_creation_form();
    }

    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new Matterhorn();
        $this->set_content_object($object);

        $success = parent :: create_content_object();

        if ($success)
        {
            $external_repository_id = (int) $this->exportValue(ExternalSync :: PROPERTY_EXTERNAL_ID);

            $external_respository_sync = new ExternalSync();
            $external_respository_sync->set_external_id($external_repository_id);
            $external_respository_sync->set_external_object_id((string) $this->exportValue(ExternalSync :: PROPERTY_EXTERNAL_OBJECT_ID));
            $external_object = $external_respository_sync->get_external_object();

            ExternalSync :: quicksave($object, $external_object, $external_repository_id);
        }

        return $success;
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        return parent :: update_content_object();
    }

}
?>