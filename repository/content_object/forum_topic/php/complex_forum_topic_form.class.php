<?php
namespace repository\content_object\forum_topic;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Path;

use repository\ComplexContentObjectItemForm;

/**
 * $Id: complex_forum_topic_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum_topic
 */
require_once Path :: get_library_path() . 'utilities.class.php';

class ComplexForumTopicForm extends ComplexContentObjectItemForm
{

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $elements = $this->get_elements();
        foreach ($elements as $element)
        {
            $this->addElement($element);
        }
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $elements = $this->get_elements();
        foreach ($elements as $element)
        {
            $this->addElement($element);
        }
    }

    public function get_elements()
    {
        $elements[] = $this->createElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('Nothing', null , Utilities :: COMMON_LIBRARIES), '', 0);
        $elements[] = $this->createElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('Sticky', null , 'repository\content_object\forum'), '', 1);
        $elements[] = $this->createElement('radio', ComplexForumTopic :: PROPERTY_TYPE, Translation :: get('Important', null , 'repository\content_object\forum'), '', 2);
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
            $defaults[ComplexForumTopic :: PROPERTY_TYPE] = $cloi->get_type() ? $cloi->get_type() : 0;
        }

        return $defaults;
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_type($values[ComplexForumTopic :: PROPERTY_TYPE]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_type($values[ComplexForumTopic :: PROPERTY_TYPE]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_type($values[ComplexForumTopic :: PROPERTY_TYPE]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_type($values[ComplexForumTopic :: PROPERTY_TYPE]);
        return parent :: update_complex_content_object_item();
    }
}

?>