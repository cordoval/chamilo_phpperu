<?php
/**
 * $Id: complex_survey_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
require_once dirname(__FILE__) . '/complex_survey.class.php';
/**
 * This class represents a form to create or update complex assessments
 */
class ComplexSurveyForm extends ComplexContentObjectItemForm
{
    const TOTAL_PROPERTIES = 1;

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('text', ComplexAssessment :: PROPERTY_TEST, Translation :: get('Test'), array("size" => "50"));
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('text', ComplexAssessment :: PROPERTY_TEST, Translation :: get('Test'), array("size" => "50"));
    }

    // Inherited
    function setDefaults($defaults = array ())
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexAssessment :: PROPERTY_TEST] = $cloi->get_test();
        }
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ComplexAssessment :: PROPERTY_TEST] = $valuearray[0];
        parent :: set_values($defaults);
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_test($values[ComplexAssessment :: PROPERTY_TEST]);
        return parent :: create_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_test($values[ComplexAssessment :: PROPERTY_TEST]);
        return parent :: update_complex_content_object_item();
    }
}
?>