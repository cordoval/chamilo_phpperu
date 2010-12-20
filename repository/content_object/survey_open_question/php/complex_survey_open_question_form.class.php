<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Translation;
use common\libraries\Path;
use repository\ComplexContentObjectItemForm;

/**
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents a complex question
 */
class ComplexSurveyOpenQuestionForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexSurveyOpenQuestion :: PROPERTY_VISIBLE, Translation :: get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();

        if (isset($cloi))
        {
            $defaults[ComplexSurveyOpenQuestion :: PROPERTY_VISIBLE] = $cloi->get_visible();
        }

        return $defaults;
    }

    // Inherited
    function create_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_visible($values[ComplexSurveyOpenQuestion :: PROPERTY_VISIBLE]);
        return parent :: create_complex_content_object_item();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexSurveyOpenQuestion :: PROPERTY_VISIBLE]);
        return parent :: create_complex_content_object_item();
    }

    function update_cloi_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexSurveyOpenQuestion :: PROPERTY_VISIBLE]);
        return parent :: update_complex_content_object_item();
    }

    // Inherited
    function update_complex_content_object_item()
    {
        $cloi = $this->get_complex_content_object_item();
        $values = $this->exportValues();
        $cloi->set_visible($values[ComplexSurveyOpenQuestion :: PROPERTY_VISIBLE]);
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

    // Inherited
    function setDefaults($defaults = array ())
    {
        $defaults = array_merge($defaults, $this->get_default_values());
        parent :: setDefaults($defaults);
    }
}
?>