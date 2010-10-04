<?php
/**
 * $Id: learning_path_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.learning_path_item
 */
class LearningPathItem extends ContentObject implements Versionable
{
    const PROPERTY_REFERENCE = 'reference_id';
    const PROPERTY_MAX_ATTEMPTS = 'max_attempts';
    const PROPERTY_MASTERY_SCORE = 'mastery_score';

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_REFERENCE, self :: PROPERTY_MAX_ATTEMPTS, self :: PROPERTY_MASTERY_SCORE);
    }

    function get_reference()
    {
        return $this->get_additional_property(self :: PROPERTY_REFERENCE);
    }

    function set_reference($reference)
    {
        $this->set_additional_property(self :: PROPERTY_REFERENCE, $reference);
    }

    function get_max_attempts()
    {
        return $this->get_additional_property(self :: PROPERTY_MAX_ATTEMPTS);
    }

    function set_max_attempts($max_attempts)
    {
        $this->set_additional_property(self :: PROPERTY_MAX_ATTEMPTS, $max_attempts);
    }

    function get_mastery_score()
    {
        return $this->get_additional_property(self :: PROPERTY_MASTERY_SCORE);
    }

    function set_mastery_score($mastery_score)
    {
        $this->set_additional_property(self :: PROPERTY_MASTERY_SCORE, $mastery_score);
    }
}
?>