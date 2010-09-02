<?php
require_once dirname(__FILE__) . '/matterhorn.class.php';

class MatterhornForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 5;

    protected function build_creation_form()
    {
        $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=1' . Matterhorn :: get_type_name();
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('BrowseStreamingVideo') . '</a>');
        $this->add_textfield(Matterhorn :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Matterhorn :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Matterhorn :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->addElement('category');

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = '$(document).ready(function ()';
        $html[] = '{';
        $html[] = '	openPopup(\'' . $link . '\');';
        $html[] = '});';
        $html[] = '</script>';

        $this->addElement('html', implode("\n", $html));
    }

    protected function build_editing_form()
    {
        $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=1' . Matterhorn :: get_type_name();
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('BrowseStreamingVideo') . '</a>');
        $this->add_textfield(Matterhorn :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Matterhorn :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Matterhorn :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Matterhorn :: PROPERTY_URL] = $lo->get_url();
            $defaults[Matterhorn :: PROPERTY_HEIGHT] = $lo->get_height();
            $defaults[Matterhorn :: PROPERTY_WIDTH] = $lo->get_width();
        }
        else
        {
            $defaults[Matterhorn :: PROPERTY_URL] = 'http://video.opencast.org/video/';
            $defaults[Matterhorn :: PROPERTY_HEIGHT] = '344';
            $defaults[Matterhorn :: PROPERTY_WIDTH] = '425';
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Matterhorn :: PROPERTY_URL] = $valuearray[3];
        $defaults[Matterhorn :: PROPERTY_HEIGHT] = $valuearray[4];
        $defaults[Matterhorn :: PROPERTY_WIDTH] = $valuearray[5];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Matterhorn();
        $object->set_url($this->exportValue(Matterhorn :: PROPERTY_URL));
        $object->set_height($this->exportValue(Matterhorn :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Matterhorn :: PROPERTY_WIDTH));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Matterhorn :: PROPERTY_URL));
        $object->set_height($this->exportValue(Matterhorn :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Matterhorn :: PROPERTY_WIDTH));
        return parent :: update_content_object();
    }

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>