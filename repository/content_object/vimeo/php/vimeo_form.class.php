<?php
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
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(Vimeo :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Vimeo :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Vimeo :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(Vimeo :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->add_textfield(Vimeo :: PROPERTY_WIDTH, Translation :: get('Width'), true, array('size' => '5'));
        $this->add_textfield(Vimeo :: PROPERTY_HEIGHT, Translation :: get('Height'), true, array('size' => '5'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Vimeo :: PROPERTY_URL] = $lo->get_url();
            $defaults[Vimeo :: PROPERTY_HEIGHT] = $lo->get_height();
            $defaults[Vimeo :: PROPERTY_WIDTH] = $lo->get_width();
        }
        else
        {
            $defaults[Vimeo :: PROPERTY_URL] = 'http://www.vimeo.com/';
            $defaults[Vimeo :: PROPERTY_HEIGHT] = '344';
            $defaults[Vimeo :: PROPERTY_WIDTH] = '425';
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Vimeo :: PROPERTY_URL] = $valuearray[3];
        $defaults[Vimeo :: PROPERTY_HEIGHT] = $valuearray[4];
        $defaults[Vimeo :: PROPERTY_WIDTH] = $valuearray[5];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Vimeo();
        $object->set_url($this->exportValue(Vimeo :: PROPERTY_URL));
        $object->set_height($this->exportValue(Vimeo :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Vimeo :: PROPERTY_WIDTH));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Vimeo :: PROPERTY_URL));
        $object->set_height($this->exportValue(Vimeo :: PROPERTY_HEIGHT));
        $object->set_width($this->exportValue(Vimeo :: PROPERTY_WIDTH));
        return parent :: update_content_object();
    }

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>
