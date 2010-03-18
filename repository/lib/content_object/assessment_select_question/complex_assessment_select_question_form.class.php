<?php
/**
 * $Id: complex_assessment_select_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.select_question
 */
require_once PATH::get_repository_path() . '/question_types/select_question/complex_select_question.class.php';

/**
 * This class represents a form to create or update complex assessments
 */
class ComplexAssessmentSelectQuestionForm extends ComplexSelectQuestionForm
{
	
public function get_elements()
    {
        $elements[] = $this->createElement('text', ComplexAssessmentSelectQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'), array("size" => "50"));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexAssessmentSelectQuestion :: PROPERTY_WEIGHT] = $cloi->get_weight() ? $cloi->get_weight() : 0;
        }
        
        return $defaults;
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentSelectQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentSelectQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentSelectQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentSelectQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }
}
?>