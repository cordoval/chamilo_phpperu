<?php
/**
 * $Id: physical_location_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.physical_location
 */
require_once dirname(__FILE__) . '/physical_location.class.php';
/**
 * This class represents a form to create or update physical_locations
 */
class PhysicalLocationForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new PhysicalLocation();
        $object->set_location($this->exportValue(PhysicalLocation :: PROPERTY_LOCATION));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_location($this->exportValue(PhysicalLocation :: PROPERTY_LOCATION));
        return parent :: update_content_object();
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[PhysicalLocation :: PROPERTY_LOCATION] = $valuearray[3];
        parent :: set_values($defaults);
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(PhysicalLocation :: PROPERTY_LOCATION, Translation :: get('Location'), true, array('size' => '100'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->add_textfield(PhysicalLocation :: PROPERTY_LOCATION, Translation :: get('Location'), true, array('size' => '100'));
        $this->addElement('category');
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[PhysicalLocation :: PROPERTY_LOCATION] = $lo->get_location();
        }
        else
        {
        	$defaults[PhysicalLocation :: PROPERTY_LOCATION] = 'grote markt, 1000, brussel, belgium';
        }
        parent :: setDefaults($defaults);
    }
}
?>
