<?php
namespace repository\content_object\youtube;

use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Path;
use common\libraries\ExternalRepositoryLauncher;
use common\libraries\Utilities;

use common\extensions\external_repository_manager\ExternalRepositoryManager;

use repository\ContentObjectForm;
use repository\ExternalRepositorySync;

/**
 * $Id: youtube_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
require_once dirname(__FILE__) . '/youtube.class.php';

class YoutubeForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $external_repositories = ExternalRepositoryLauncher :: get_links(Youtube :: get_type_name(), true);
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->addElement('hidden', ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_ID);
        $this->addElement('hidden', ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID);

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
        $object = new Youtube();
        $this->set_content_object($object);

        $success = parent :: create_content_object();

        if ($success)
        {
            $external_repository_id = (int) $this->exportValue(ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_ID);

            $external_respository_sync = new ExternalRepositorySync();
            $external_respository_sync->set_external_repository_id($external_repository_id);
            $external_respository_sync->set_external_repository_object_id((string) $this->exportValue(ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID));
            $external_object = $external_respository_sync->get_external_repository_object();

            ExternalRepositorySync :: quicksave($object, $external_object, $external_repository_id);
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