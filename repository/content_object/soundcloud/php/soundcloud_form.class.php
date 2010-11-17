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

        $external_repositories = $this->get_external_repositories();
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->add_textfield(Soundcloud :: PROPERTY_TRACK_ID, Translation :: get('TrackId'), true, array('size' => '100'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        //        $link = Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=1'/* . Soundcloud :: get_type_name()*/;
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        //        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('BrowseStreamingVideo') . '</a>');
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

    function get_external_repositories()
    {
        $instances = RepositoryDataManager :: get_instance()->retrieve_active_external_repositories(Soundcloud :: get_type_name());

        if ($instances->size() == 0)
        {
            return null;
        }
        else
        {
            $html = array();
            $buttons = array();

            while ($instance = $instances->next_result())
            {
                $link = Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=' . $instance->get_id();
                $image = Theme :: get_image_path(ExternalRepositoryManager :: get_namespace($instance->get_type())) . 'logo/16.png';
                $title = Translation :: get('BrowseObject', array('OBJECT' => $instance->get_title()), Utilities :: COMMON_LIBRARIES);
                $buttons[] = '<a class="button normal_button upload_button" style="background-image: url(' . $image . ');" onclick="javascript:openPopup(\'' . $link . '\');"> ' . $title . '</a>';
            }

            $html[] = '<div style="margin-bottom: 10px;">' . implode(' ', $buttons) . '</div>';

            if ($instances->size() == 1 && $this->get_form_type() == self :: TYPE_CREATE)
            {
                $html[] = '<script type="text/javascript">';
                $html[] = '$(document).ready(function ()';
                $html[] = '{';
                $html[] = '	openPopup(\'' . $link . '\');';
                $html[] = '});';
                $html[] = '</script>';
            }

            return implode("\n", $html);
        }
    }

}
?>