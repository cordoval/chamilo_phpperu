<?php
namespace repository\content_object\learning_path;
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/../learning_path_builder.class.php';
//require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
/**
 */
class LearningPathBuilderDeleterComponent extends LearningPathBuilder
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $deleter = ComplexBuilderComponent::factory(ComplexBuilderComponent::DELETER_COMPONENT, $this);
        $deleter->run();
    }
}
?>