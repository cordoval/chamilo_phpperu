<?php
namespace repository\content_object\survey_multiple_choice_question;

use common\libraries\Path;

/**
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an option in a multiple choice question.
 */
class SurveyMultipleChoiceQuestionOption
{
    const PROPERTY_VALUE = 'value';

    private $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    function get_value()
    {
        return $this->value;
    }
}
?>