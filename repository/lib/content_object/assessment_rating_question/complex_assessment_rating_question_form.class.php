<?php
/**
 * $Id: complex_assessment_rating_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.rating_question
 */
require_once PATH :: get_repository_path() . 'question_types/rating_question/complex_rating_question_form.class.php';

/**
 * This class represents a complex question
 */
class ComplexAssessmentRatingQuestionForm extends ComplexRatingQuestionForm
{

 	public function get_elements()
    {
        $elements[] = $this->createElement('text', ComplexAssessmentRatingQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'), array("size" => "50"));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexAssessmentRatingQuestion :: PROPERTY_WEIGHT] = $cloi->get_weight() ? $cloi->get_weight() : 0;
        }
        
        return $defaults;
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentRatingQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentRatingQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentRatingQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentRatingQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }
}
?>