<?php
/**
 * $Id: complex_rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.rating_question
 */
/**
 * This class represents a complex exercise (used to create complex learning objects)
 */
class ComplexRatingQuestion extends ComplexContentObjectItem
{
    const PROPERTY_WEIGHT = 'weight';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_WEIGHT);
    }

    function get_weight()
    {
        return $this->get_additional_property(self :: PROPERTY_WEIGHT);
    }

    function set_weight($value)
    {
        $this->set_additional_property(self :: PROPERTY_WEIGHT, $value);
    }
}
?>