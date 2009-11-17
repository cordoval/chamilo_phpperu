<?php
/**
 * $Id: complex_learning_path_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.learning_path_item
 */

class ComplexLearningPathItem extends ComplexContentObjectItem
{
    const PROPERTY_PREREQUISITES = 'prerequisites';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_PREREQUISITES);
    }

    function get_prerequisites()
    {
        return $this->get_additional_property(self :: PROPERTY_PREREQUISITES);
    }

    function set_prerequisites($value)
    {
        $this->set_additional_property(self :: PROPERTY_PREREQUISITES, $value);
    }
}
?>