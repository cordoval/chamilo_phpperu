<?php
/**
 * $Id: rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.rating_question
 */
/**
 * This class represents an open question
 */
class RatingQuestion extends ContentObject
{
    const PROPERTY_LOW = 'low';
    const PROPERTY_HIGH = 'high';
    const PROPERTY_CORRECT = 'correct';

    function get_allowed_types()
    {
        return array();
    }

    function get_low()
    {
        return $this->get_additional_property(self :: PROPERTY_LOW);
    }

    function get_high()
    {
        return $this->get_additional_property(self :: PROPERTY_HIGH);
    }

    function set_low($value)
    {
        $this->set_additional_property(self :: PROPERTY_LOW, $value);
    }

    function set_high($value)
    {
        $this->set_additional_property(self :: PROPERTY_HIGH, $value);
    }

    function get_correct()
    {
        return $this->get_additional_property(self :: PROPERTY_CORRECT);
    }

    function set_correct($value)
    {
        $this->set_additional_property(self :: PROPERTY_CORRECT, $value);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOW, self :: PROPERTY_HIGH, self :: PROPERTY_CORRECT);
    }
}
?>