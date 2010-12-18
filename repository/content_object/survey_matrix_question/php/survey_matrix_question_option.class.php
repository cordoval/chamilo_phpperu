<?php
namespace repository\content_object\survey_matrix_question;

use common\libraries\Path;

/**
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an option in a matrix question.
 */
class SurveyMatrixQuestionOption
{
    const PROPERTY_VALUE = 'value';

    private $value;

    /**
     * Creates a new option for a matrix question
     * @param string $value The value of the option
     * @param int $matches The index of the match corresponding to this option
     */
    function __construct($value = '')
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