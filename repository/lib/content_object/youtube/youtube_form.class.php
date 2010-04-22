<?php
/**
 * $Id: youtube_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
require_once dirname(__FILE__) . '/youtube.class.php';

class YoutubeForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 5;

    protected function build_creation_form()
    {
        $link = PATH :: get(WEB_PATH) . 'application/common/streaming_media_manager/index.php?' . StreamingMediaBrowser::PARAM_TYPE . '=' . Youtube::get_type_name();
    	parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" href="' . $link .'"> '. Translation :: get('BrowseStreamingVideo') .'</a>');
        $this->add_textfield(Youtube :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Youtube :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Youtube :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('static', null, null, '<a class="button normal_button upload_button" href=""> '. Translation :: get('BrowseStreamingVideo') .'</a>');       
        $this->add_textfield(Youtube :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Youtube :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Youtube :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Youtube :: PROPERTY_URL] = $lo->get_url();
            $defaults[Youtube :: PROPERTY_HEIGHT] = $lo->get_height();
            $defaults[Youtube :: PROPERTY_WIDTH] = $lo->get_width();
        }
        else
        {
            $defaults[Youtube :: PROPERTY_URL] = 'http://www.youtube.com/watch?v=';
            $defaults[Youtube :: PROPERTY_HEIGHT] = '344';
            $defaults[Youtube :: PROPERTY_WIDTH] = '425';
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Youtube :: PROPERTY_URL] = $valuearray[3];
        $defaults[Youtube :: PROPERTY_HEIGHT] = $valuearray[4];
        $defaults[Youtube :: PROPERTY_WIDTH] = $valuearray[5];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Youtube();
        $object->set_url($this->exportValue(Youtube :: PROPERTY_URL));
        $object->set_height($this->exportValue(Youtube :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Youtube :: PROPERTY_WIDTH));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Youtube :: PROPERTY_URL));
        $object->set_height($this->exportValue(Youtube :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Youtube :: PROPERTY_WIDTH));
        return parent :: update_content_object();
    }

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>