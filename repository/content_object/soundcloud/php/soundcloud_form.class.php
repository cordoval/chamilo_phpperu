<?php
namespace repository\content_object\soundcloud;

use repository\ExternalRepository;
use repository\RepositoryDataManager;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\Theme;
use common\libraries\ExternalRepositoryLauncher;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use repository\ContentObjectForm;
use common\libraries\Utilities;

/**
 * $Id: soundcloud_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.soundcloud
 */
require_once dirname(__FILE__) . '/soundcloud.class.php';

class SoundcloudForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 5;

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $external_repositories = ExternalRepositoryLauncher :: get_links(Soundcloud :: get_type_name(), true);
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->add_textfield(Soundcloud :: PROPERTY_TRACK_ID, Translation :: get('TrackId'), true, array('size' => '100'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $external_repositories = ExternalRepositoryLauncher :: get_links(Soundcloud :: get_type_name());
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->add_textfield(Soundcloud :: PROPERTY_TRACK_ID, Translation :: get('TrackId'), true, array('size' => '100'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Soundcloud :: PROPERTY_TRACK_ID] = $lo->get_track_id();
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $object = new Soundcloud();
        $object->set_track_id($this->exportValue(Soundcloud :: PROPERTY_TRACK_ID));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_track_id($this->exportValue(Soundcloud :: PROPERTY_TRACK_ID));
        return parent :: update_content_object();
    }
}
?>