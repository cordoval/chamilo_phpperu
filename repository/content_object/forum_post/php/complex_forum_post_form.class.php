<?php
/**
 * @package repository.learningobject
 * @subpackage forum_post
 */
require_once Path :: get_library_path() . 'utilities.class.php';

class ComplexForumPostForm extends ComplexContentObjectItemForm
{
    const TOTAL_PROPERTIES = 0;

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('text', ComplexForumPost :: PROPERTY_REPLY_ON_POST, Translation :: get('reply_on_post'));
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('text', ComplexForumPost :: PROPERTY_REPLY_ON_POST, Translation :: get('reply_on_post'));
    }

    public function get_elements()
    {
        $elements[] = $this->createElement('hidden', ComplexForumPost :: PROPERTY_REPLY_ON_POST, 0);
        return $elements;
    }

    // Inherited
    function setDefaults($defaults = array ())
    {
        $defaults = $this->get_default_values($defaults);
        parent :: setDefaults($defaults);
    }

    function get_default_values($defaults = array ())
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexForumPost :: PROPERTY_REPLY_ON_POST] = $cloi->get_reply_on_post() ? $cloi->get_reply_on_post() : 0;
        }
        
        return $defaults;
    }

    function set_csv_values($valuearray)
    {
        $defaults[ComplexForumPost :: PROPERTY_REPLY_ON_POST] = $valuearray[0];
        parent :: set_values($defaults);
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_reply_on_post($values[ComplexForumPost :: PROPERTY_REPLY_ON_POST]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_reply_on_post($values[ComplexForumPost :: PROPERTY_REPLY_ON_POST]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_reply_on_post($values[ComplexForumPost :: PROPERTY_REPLY_ON_POST]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_reply_on_post($values[ComplexForumPost :: PROPERTY_REPLY_ON_POST]);
        return parent :: update_complex_content_object_item();
    }
}

?>