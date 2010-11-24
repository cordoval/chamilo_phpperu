<?php
namespace repository\content_object\glossary;

use repository\ContentObjectForm;
/**
 * $Id: glossary_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.glossary
 */
require_once dirname(__FILE__) . '/glossary.class.php';
/**
 * This class represents a form to create or update glossarys
 */
class GlossaryForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new Glossary();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

}
?>