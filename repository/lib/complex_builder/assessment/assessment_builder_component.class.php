<?php
/**
 * $Id: assessment_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class AssessmentBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Assessment', $component_name, $builder);
    }
}

?>