<?php
namespace repository\content_object\vimeo;

use repository\ContentObjectForm;
use repository\ExternalRepositorySync;

use common\libraries\Translation;
use common\libraries\ExternalRepositoryLauncher;
use common\libraries\Utilities;

/**
 * $Id: vimeo_form.class.php 2010-06-08
 * package repository.lib.content_object.vimeo
 * @author Shoira Mukhsinova
 */
require_once dirname(__FILE__) . '/vimeo.class.php';

class VimeoForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 5;

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        
        $external_repositories = ExternalRepositoryLauncher :: get_links(Vimeo :: get_type_name(), true);
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
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Vimeo();
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

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>
