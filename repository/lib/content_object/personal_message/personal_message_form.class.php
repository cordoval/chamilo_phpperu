<?php
/**
 * $Id: personal_message_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.personal_message
 */
require_once dirname(__FILE__) . '/personal_message.class.php';
/**
 * This class represents a form to create or update personal messages
 */
class PersonalMessageForm extends ContentObjectForm
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
        $object = new PersonalMessage();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>
