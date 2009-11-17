<?php
/**
 * $Id: description_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.description
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/description.class.php';
/**
 * A form to create/update a description
 */
class DescriptionForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        parent :: set_values($defaults);
    }

    // Inherited
    function create_content_object()
    {
        $object = new Description();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}

?>
