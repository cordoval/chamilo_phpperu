<?php
namespace repository\content_object\assessment_multiple_choice_question;

use common\libraries\Translation;
use common\libraries\Path;
use repository\ComplexContentObjectItemForm;

/**
 * $Id: complex_assessment_multiple_choice_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.multiple_choice_question
 */
/**
 * This class represents a form to create or update complex assessments
 */
class ComplexAssessmentMultipleChoiceQuestionForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('text', ComplexAssessmentMultipleChoiceQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'), array(
                "size" => "50"));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();

        if (isset($cloi))
        {
            $defaults[ComplexAssessmentMultipleChoiceQuestion :: PROPERTY_WEIGHT] = $cloi->get_weight() ? $cloi->get_weight() : 0;
        }

        return $defaults;
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentMultipleChoiceQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentMultipleChoiceQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentMultipleChoiceQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentMultipleChoiceQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }

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

    function setDefaults($defaults = array ())
    {
        $defaults = array_merge($defaults, $this->get_default_values());
        parent :: setDefaults($defaults);
    }
}