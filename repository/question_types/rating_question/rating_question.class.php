<?php
/**
 * $Id: rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.rating_question
 */
/**
 * This class represents an open question
 */
abstract class RatingQuestion extends ContentObject implements Versionable
{
    const PROPERTY_LOW = 'low';
    const PROPERTY_HIGH = 'high';

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

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOW, self :: PROPERTY_HIGH);
    }
}
?>