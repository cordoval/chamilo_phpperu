<?php
require_once dirname(__FILE__) . '/matterhorn.class.php';

class MatterhornForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 6;

    protected function build_creation_form()
    {
        $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=1' . Matterhorn :: get_type_name();
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('BrowseStreamingVideo') . '</a>');
        $this->add_textfield(Matterhorn :: PROPERTY_MATTERHORN_ID, Translation :: get('MatterhornId'), true, array('size' => '255'));
        $this->add_textfield(Matterhorn :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Matterhorn :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->add_textfield(Matterhorn :: PROPERTY_THUMBNAIL, Translation :: get('Thumbnail'), true, array('size' => '255'));
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
        $this->add_textfield(Matterhorn :: PROPERTY_MATTERHORN_ID, Translation :: get('URL'), true, array('size' => '255'));
        $this->add_textfield(Matterhorn :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Matterhorn :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->add_textfield(Matterhorn :: PROPERTY_THUMBNAIL, Translation :: get('Thumbnail'), true, array('size' => '255'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Matterhorn :: PROPERTY_MATTERHORN_ID] = $lo->get_matterhorn_id();
            $defaults[Matterhorn :: PROPERTY_HEIGHT] = $lo->get_height();
            $defaults[Matterhorn :: PROPERTY_WIDTH] = $lo->get_width();
            $defaults[Matterhorn :: PROPERTY_THUMBNAIL] = $lo->get_thumbnail();
        }
        else
        {
            $defaults[Matterhorn :: PROPERTY_HEIGHT] = '596';
            $defaults[Matterhorn :: PROPERTY_WIDTH] = '620';
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Matterhorn :: PROPERTY_MATTERHORN_ID] = $valuearray[3];
        $defaults[Matterhorn :: PROPERTY_HEIGHT] = $valuearray[4];
        $defaults[Matterhorn :: PROPERTY_WIDTH] = $valuearray[5];
        $defaults[Matterhorn :: PROPERTY_THUMBNAIL] = $valuearray[6];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Matterhorn();
        $object->set_matterhorn_id($this->exportValue(Matterhorn :: PROPERTY_MATTERHORN_ID));
        $object->set_height($this->exportValue(Matterhorn :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Matterhorn :: PROPERTY_WIDTH));
        $object->set_thumbnail($this->exportValue(Matterhorn :: PROPERTY_THUMBNAIL));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Matterhorn :: PROPERTY_MATTERHORN_ID));
        $object->set_height($this->exportValue(Matterhorn :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Matterhorn :: PROPERTY_WIDTH));
        $object->set_thumbnail($this->exportValue(Matterhorn :: PROPERTY_THUMBNAIL));
        return parent :: update_content_object();
    }

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>