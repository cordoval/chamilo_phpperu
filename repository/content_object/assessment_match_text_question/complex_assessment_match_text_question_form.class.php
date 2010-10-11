<?php
/**
 * @package repository.lib.content_object.match_text_question
 */
require_once dirname(__FILE__) . '/main.php'; 

/**
 * This class represents a form to create or update complex assessments
 */
class ComplexAssessmentMatchTextQuestionForm extends ComplexContentObjectItemForm
{

	public function get_elements()
    {
        $elements[] = $this->createElement('text', ComplexAssessmentMatchTextQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'), array("size" => "50"));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexAssessmentMatchTextQuestion :: PROPERTY_WEIGHT] = $cloi->get_weight() ? $cloi->get_weight() : 0;
        }
        
        return $defaults;
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentMatchTextQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentMatchTextQuestion :: PROPERTY_WEIGHT]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_weight($values[ComplexAssessmentMatchTextQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_weight($values[ComplexAssessmentMatchTextQuestion :: PROPERTY_WEIGHT]);
        return parent :: update_complex_content_object_item();
    }
}
?>