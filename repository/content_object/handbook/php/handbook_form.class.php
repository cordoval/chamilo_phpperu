<?php
namespace repository\content_object\handbook;

use repository\ContentObjectForm;
use repository\ContentObject;
use common\libraries\Translation;
use repository\content_object\handbook_item\HandbookItem;
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
//        $object->set_uuid($this->exportValue(Handbook :: PROPERTY_UUID));
        $object->set_uuid();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

     function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_uuid($this->exportValue(Handbook :: PROPERTY_UUID));
        return parent :: update_content_object();
    }

    function build_creation_form($default_content_object = null)
    {
        parent :: build_creation_form();
//        $this->addElement('category', Translation :: get('Properties'));
////        $this->addElement('text', HandbookItem :: PROPERTY_UUID, Translation :: get('Uuid'));
//        $this->addElement('category');
    }

    function build_editing_form($object)
    {
        parent :: build_editing_form();
//        $this->addElement('category', Translation :: get('Properties'));
//        $this->addElement('text', HandbookItem :: PROPERTY_REFERENCE, Translation :: get('Reference'));
//        $this->addElement('category');
    }

//    function setDefaults($defaults = array ())
//    {
//        $object = $this->get_content_object();
//        $defaults[HandbookItem :: PROPERTY_UUID] = $object->get_uuid();
//        parent :: setDefaults($defaults);
//    }
}
?>