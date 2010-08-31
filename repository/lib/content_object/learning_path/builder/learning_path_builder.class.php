<?php
/**
 * $Id: learning_path_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path
 */

class LearningPathBuilder extends ComplexBuilder implements ComplexMenuSupport
{
    const ACTION_CREATE_LP_ITEM = 'item_creator';
    const ACTION_BUILD_PREREQUISITES = 'prerequisites_builder';
    const ACTION_SET_MASTERY_SCORE = 'mastery_score_setter';

    function get_prerequisites_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BUILD_PREREQUISITES, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    function get_mastery_score_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_SET_MASTERY_SCORE, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_BUILDER_ACTION;
    }

}
?>