<?php
/**
 * $Id: link_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */
require_once dirname(__FILE__) . '/link.class.php';
class LinkForm extends ContentObjectForm
{
    const TOTAL_PROPERTIES = 3;

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(Link :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(Link :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Link :: PROPERTY_URL] = $lo->get_url();
        }
        else
        {
            $defaults[Link :: PROPERTY_URL] = 'http://';
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Link :: PROPERTY_URL] = $valuearray[3];
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Link();
        $object->set_url($this->exportValue(Link :: PROPERTY_URL));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Link :: PROPERTY_URL));
        return parent :: update_content_object();
    }

    function validatecsv($value)
    {
        return parent :: validatecsv($value);
    }

}
?>
