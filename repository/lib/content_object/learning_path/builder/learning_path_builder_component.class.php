<?php
/**
 * $Id: learning_path_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class LearningPathBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('LearningPath', $component_name, $builder);
    }

    function get_prerequisites_url($selected_complex_content_object_item)
    {
        return $this->get_parent()->get_prerequisites_url($selected_complex_content_object_item);
    }

    function get_mastery_score_url($selected_complex_content_object_item)
    {
        return $this->get_parent()->get_mastery_score_url($selected_complex_content_object_item);
    }
}

?>