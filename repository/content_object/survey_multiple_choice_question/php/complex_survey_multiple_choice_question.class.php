<?php
namespace repository\content_object\survey_multiple_choice_question;

use common\libraries\Path;
use repository\ComplexContentObjectItem;

/**
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexSurveyMultipleChoiceQuestion extends ComplexContentObjectItem
{

    const PROPERTY_VISIBLE = 'visible';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_VISIBLE);
    }

    function get_visible()
    {
        return $this->get_additional_property(self :: PROPERTY_VISIBLE);
    }

    function set_visible($value)
    {
        $this->set_additional_property(self :: PROPERTY_VISIBLE, $value);
    }

    function is_visible()
    {
        return $this->get_visible() == 1;
    }

    function toggle_visibility()
    {
        $this->set_visible(! $this->get_visible());
    }

}
?>