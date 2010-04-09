<?php
/**
 * $Id: peer_assessment_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.peer_assessment
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class PeerAssessmentBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('PeerAssessment', $component_name, $builder);
    }
}

?>