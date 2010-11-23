<?php

namespace repository\content_object\assessment_open_question;
use common\libraries\Path;
use repository\OpenQuestionForm;
use common\libraries\Translation;

/**
 * $Id: assessment_open_question_form.class.php$ $
 * @package repository.lib.content_object.assessment_open_question
 */
require_once Path :: get_repository_path() . '/question_types/open_question/open_question_form.class.php';

/**
 * This class represents a form to create or update open questions
 */
class AssessmentOpenQuestionForm extends OpenQuestionForm
{

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object->get_id() != null)
        {
            $defaults[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
            $defaults[AssessmentOpenQuestion :: PROPERTY_FEEDBACK] = $object->get_feedback();
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
        $this->addElement('category', Translation :: get('Properties'));
        $types = AssessmentOpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement('radio', AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE, '', $type_label, $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '<br />', false);

        $this->add_html_editor(AssessmentOpenQuestion :: PROPERTY_FEEDBACK, Translation :: get('Feedback'), false);

        $this->addElement('category');
    }

    // Inherited
    function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
        $types = AssessmentOpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement('radio', AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE, '', $type_label, $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '<br />', false);

        $this->add_html_editor(AssessmentOpenQuestion :: PROPERTY_FEEDBACK, Translation :: get('Feedback'), false);

        $this->addElement('category');
    }

    // Inherited
    function create_content_object()
    {
        $object = new AssessmentOpenQuestion();
        
        $values = $this->exportValues();
        $object->set_question_type($values[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE]);
        $object->set_feedback($values[AssessmentOpenQuestion :: PROPERTY_FEEDBACK]);
        
        $this->set_content_object($object);
        return parent :: create_content_object($object);
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        
        $values = $this->exportValues();
        $object->set_question_type($values[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE]);
        $object->set_feedback($values[AssessmentOpenQuestion :: PROPERTY_FEEDBACK]);
        
        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
?>