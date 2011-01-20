<?php
namespace repository\content_object\survey_matching_question;

use common\libraries\Path;

/**
 * @package repository.content_object.survey_matching_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an option in a matching question.
 */
class SurveyMatchingQuestionOption
{
    const PROPERTY_VALUE = 'value';

    private $value;

    /**
     * Creates a new option for a matching question
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $weight The weight of this answer in the question
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