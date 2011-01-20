<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Path;

/**
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an option in a multiple choice question.
 */
class SurveySelectQuestionOption
{
    const PROPERTY_VALUE = 'value';

    private $value;

    /**
     * Creates a new option for a multiple choice question
     * @param string $value The value of the option
     * @param boolean $correct True if the value of this option is a correct
     * answer to the question
     */
    function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
        return $this->value;
    }
}
?>