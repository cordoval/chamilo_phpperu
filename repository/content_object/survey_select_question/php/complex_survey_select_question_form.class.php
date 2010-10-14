<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Translation;

/**
 * $Id: complex_survey_select_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_select_question
 */
require_once Path :: get_repository_path() . '/question_types/select_question/complex_select_question_form.class.php';

/**
 * This class represents a form to create or update complex assessments
 */
class ComplexSurveySelectQuestionForm extends ComplexSelectQuestionForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexSurveySelectQuestion :: PROPERTY_VISIBLE, Translation :: get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();

        if (isset($cloi))
        {
            $defaults[ComplexSurveySelectQuestion :: PROPERTY_VISIBLE] = $cloi->get_visible();
        }

        return $defaults;
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_visible($values[ComplexSurveySelectQuestion :: PROPERTY_VISIBLE]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexSurveySelectQuestion :: PROPERTY_VISIBLE]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexSurveySelectQuestion :: PROPERTY_VISIBLE]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_visible($values[ComplexSurveySelectQuestion :: PROPERTY_VISIBLE]);
        return parent :: update_complex_content_object_item();
    }
}
?>