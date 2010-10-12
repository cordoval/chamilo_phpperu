P<?php
require_once dirname(__FILE__) . '/photobucket.class.php';

class PhotobucketForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 6;

    protected function build_creation_form()
    {
        $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=1' . Photobucket :: get_type_name();
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('BrowseStreamingVideo') . '</a>');
        $this->add_textfield(Photobucket :: PROPERTY_PHOTOBUCKET_ID, Translation :: get('PhotobucketId'), true, array('size' => '255'));
        $this->add_textfield(Photobucket :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Photobucket :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->add_textfield(Photobucket :: PROPERTY_THUMBNAIL, Translation :: get('Thumbnail'), true, array('size' => '255'));
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
        $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=1' . Photobucket :: get_type_name();
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('BrowseStreamingVideo') . '</a>');
        $this->add_textfield(Photobucket :: PROPERTY_PHOTOBUCKET_ID, Translation :: get('PhotobucketId'), true, array('size' => '255'));
        $this->add_textfield(Photobucket :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Photobucket :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->add_textfield(Photobucket :: PROPERTY_THUMBNAIL, Translation :: get('Thumbnail'), true, array('size' => '255'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Photobucket :: PROPERTY_PHOTOBUCKET_ID] = $lo->get_photobucket_id();
            $defaults[Photobucket :: PROPERTY_HEIGHT] = $lo->get_height();
            $defaults[Photobucket :: PROPERTY_WIDTH] = $lo->get_width();
            $defaults[Photobucket :: PROPERTY_THUMBNAIL] = $lo->get_thumbnail();
        }
        else
        {
            $defaults[Photobucket :: PROPERTY_HEIGHT] = '596';
            $defaults[Photobucket :: PROPERTY_WIDTH] = '620';
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Photobucket :: PROPERTY_PHOTOBUCKET_ID] = $valuearray[3];
        $defaults[Photobucket :: PROPERTY_HEIGHT] = $valuearray[4];
        $defaults[Photobucket :: PROPERTY_WIDTH] = $valuearray[5];
        $defaults[Photobucket :: PROPERTY_THUMBNAIL] = $valuearray[6];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Photobucket();
        $object->set_photobucket_id($this->exportValue(Photobucket :: PROPERTY_PHOTOBUCKET_ID));
        $object->set_height($this->exportValue(Photobucket :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Photobucket :: PROPERTY_WIDTH));
        $object->set_thumbnail($this->exportValue(Photobucket :: PROPERTY_THUMBNAIL));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Photobucket :: PROPERTY_PHOTOBUCKET_ID));
        $object->set_height($this->exportValue(Photobucket :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Photobucket :: PROPERTY_WIDTH));
        $object->set_thumbnail($this->exportValue(Photobucket :: PROPERTY_THUMBNAIL));
        return parent :: update_content_object();
    }

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>