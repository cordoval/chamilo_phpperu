<?php
namespace repository\content_object\forum_post;

use common\libraries\Translation;
use common\libraries\Path;

use repository\ComplexContentObjectItemForm;

/**
 * @package repository.learningobject
 * @subpackage forum_post
 */
require_once Path :: get_library_path() . 'utilities.class.php';

class ComplexForumPostForm extends ComplexContentObjectItemForm
{

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('text', ComplexForumPost :: PROPERTY_REPLY_ON_POST, Translation :: get('ReplyOnPost'));
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('text', ComplexForumPost :: PROPERTY_REPLY_ON_POST, Translation :: get('ReplyOnPost'));
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