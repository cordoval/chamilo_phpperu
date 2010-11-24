<?php
namespace repository\content_object\introduction;

use repository\ContentObjectForm;

/**
 * $Id: introduction_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.introduction
 */
require_once dirname(__FILE__) . '/introduction.class.php';
/**
 * A form to create/update a introduction
 */
class IntroductionForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new Introduction();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}

?>