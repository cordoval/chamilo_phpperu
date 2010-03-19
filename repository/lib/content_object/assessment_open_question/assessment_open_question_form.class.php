<?php
/**
 * $Id: assessment_open_question_form.class.php$ $
 * @package repository.lib.content_object.assessment_open_question
 */
require_once PATH :: get_repository_path() . '/question_types/open_question/open_question_form.class.php';

/**
 * This class represents a form to create or update open questions
 */
class AssessmentOpenQuestionForm extends OpenQuestionForm
{

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE] = $valuearray[3];

        parent :: set_values($defaults);
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
        }
        else
        {
            $defaults[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE] = AssessmentOpenQuestion :: TYPE_OPEN;
        }

        parent :: setDefaults($defaults);
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $types = AssessmentOpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement('radio', AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE, '', $type_label, $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '<br />', false);
        $this->addElement('category');
    }

    // Inherited
    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $types = AssessmentOpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement('radio', AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE, '', $type_label, $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '<br />', false);
        $this->addElement('category');
    }

    // Inherited
    function create_content_object()
    {
        $object = new AssessmentOpenQuestion();

        $values = $this->exportValues();
        $object->set_question_type($values[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE]);

        $this->set_content_object($object);
        return parent :: create_content_object($object);
    }

    function update_content_object()
    {
        $object = $this->get_content_object();

        $values = $this->exportValues();
        $object->set_question_type($values[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE]);

        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
?>


