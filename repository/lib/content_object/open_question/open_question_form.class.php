<?php
/**
 * $Id: open_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.open_question
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
        $defaults[OpenQuestion :: PROPERTY_QUESTION_TYPE] = $valuearray[3];
        
        parent :: set_values($defaults);
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[OpenQuestion :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
        }
        else
        {
            $defaults[OpenQuestion :: PROPERTY_QUESTION_TYPE] = OpenQuestion :: TYPE_OPEN;
        }
        
        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $types = OpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement('radio', OpenQuestion :: PROPERTY_QUESTION_TYPE, '', $type_label, $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '<br />', false);
        $this->addElement('category');
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $types = OpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement('radio', OpenQuestion :: PROPERTY_QUESTION_TYPE, '', $type_label, $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '<br />', false);
        $this->addElement('category');
    }

    // Inherited
    function create_content_object()
    {
        $object = new OpenQuestion();
        
        $values = $this->exportValues();
        $object->set_question_type($values[OpenQuestion :: PROPERTY_QUESTION_TYPE]);
        
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        
        $values = $this->exportValues();
        $object->set_question_type($values[OpenQuestion :: PROPERTY_QUESTION_TYPE]);
        
        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
?>
