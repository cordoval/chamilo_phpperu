<?php
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class PeerAssessmentBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('PeerAssessment', $component_name, $builder);
    }
}

?>
