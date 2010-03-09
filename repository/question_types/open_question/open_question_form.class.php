<?php
/**
 * $Id: assessment_open_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.open_question
 */
require_once dirname(__FILE__) . '/open_question.class.php';
/**
 * This class represents a form to create or update open questions
 */
class OpenQuestionForm extends ContentObjectForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        
        parent :: set_values($defaults);
    }

    function setDefaults($defaults = array ())
    {
    	parent :: setDefaults($defaults);
    }

    function build_creation_form()
    {
        parent :: build_creation_form();

        $this->addElement('category');
        
    }

    // Inherited
    function build_editing_form()
    {
        parent :: build_editing_form();       
        $this->addElement('category');
    }

    // Inherited
    function create_content_object($object)
    {
        $this->set_content_object($object);
    	return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
    	$this->set_content_object($object);
        
    	return parent :: update_content_object();
    }
}
?>
