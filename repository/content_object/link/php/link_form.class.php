<?php
namespace repository\content_object\link;

use common\libraries\Translation;

use repository\ContentObjectForm;

/**
 * $Id: link_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */
class LinkForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(Link :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
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
}
?>