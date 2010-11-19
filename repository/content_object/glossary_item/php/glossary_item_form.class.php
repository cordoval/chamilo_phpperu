<?php
namespace repository\content_object\glossary_item;

use repository\ContentObjectForm;
/**
 * $Id: glossary_item_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.glossary_item
 */
require_once dirname(__FILE__) . '/glossary_item.class.php';
/**
 * This class represents a form to create or update glossary_items
 */
class GlossaryItemForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new GlossaryItem();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

}
?>