<?php
/**
 * $Id: handbook_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.handbook
 */
require_once dirname(__FILE__) . '/handbook.class.php';
/**
 * This class represents a form to create or update handbooks
 */
class HandbookForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new Handbook();
        $object->set_uuid($this->exportValue(Handbook :: PROPERTY_UUID));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[HandbookItem :: PROPERTY_UUID] = $valuearray[3];
        parent :: set_values($defaults);
    }


     function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_uuid($this->exportValue(HandbookItem :: PROPERTY_UUID));
        return parent :: update_content_object();
    }

    function build_creation_form($default_content_object = null)
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('text', HandbookItem :: PROPERTY_UUID, Translation :: get('Uuid'));
        $this->addElement('category');
    }

    function build_editing_form($object)
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('text', HandbookItem :: PROPERTY_REFERENCE, Translation :: get('Reference'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[HandbookItem :: PROPERTY_UUID] = $object->get_uuid();
        parent :: setDefaults($defaults);
    }
}
?>