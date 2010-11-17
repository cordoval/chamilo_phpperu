<?php
namespace repository\content_object\matterhorn;

use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Path;
use common\libraries\ExternalRepositoryLauncher;

use common\extensions\external_repository_manager\ExternalRepositoryManager;

use repository\ContentObjectForm;

require_once dirname(__FILE__) . '/matterhorn.class.php';

class MatterhornForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 6;

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $external_repositories = ExternalRepositoryLauncher :: get_links(Matterhorn :: get_type_name(), true);
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->add_textfield(Matterhorn :: PROPERTY_MATTERHORN_ID, Translation :: get('MatterhornId'), true, array('size' => '255'));
        $this->add_textfield(Matterhorn :: PROPERTY_THUMBNAIL, Translation :: get('Thumbnail'), true, array('size' => '255'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $external_repositories = ExternalRepositoryLauncher :: get_links(Matterhorn :: get_type_name());
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->add_textfield(Matterhorn :: PROPERTY_MATTERHORN_ID, Translation :: get('URL'), true, array('size' => '255'));
        $this->add_textfield(Matterhorn :: PROPERTY_THUMBNAIL, Translation :: get('Thumbnail'), true, array('size' => '255'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Matterhorn :: PROPERTY_MATTERHORN_ID] = $lo->get_matterhorn_id();
            $defaults[Matterhorn :: PROPERTY_THUMBNAIL] = $lo->get_thumbnail();
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Matterhorn :: PROPERTY_MATTERHORN_ID] = $valuearray[3];
        $defaults[Matterhorn :: PROPERTY_THUMBNAIL] = $valuearray[6];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Matterhorn();
        $object->set_matterhorn_id($this->exportValue(Matterhorn :: PROPERTY_MATTERHORN_ID));
        $object->set_thumbnail($this->exportValue(Matterhorn :: PROPERTY_THUMBNAIL));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_matterhorn_id($this->exportValue(Matterhorn :: PROPERTY_MATTERHORN_ID));
        //        $object->set_height($this->exportValue(Matterhorn :: PROPERTY_HEIGHT));
        //        $object->set_width($this->exportValue(Matterhorn :: PROPERTY_WIDTH));
        $object->set_thumbnail($this->exportValue(Matterhorn :: PROPERTY_THUMBNAIL));
        return parent :: update_content_object();
    }

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>