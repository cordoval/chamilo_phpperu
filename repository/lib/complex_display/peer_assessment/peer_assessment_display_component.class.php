<?php
require_once dirname(__FILE__) . '/../complex_display_component.class.php';
/**
 * @author Nick Van Loocke
 */

class PeerAssessmentDisplayComponent extends ComplexDisplayComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('PeerAssessment', $component_name, $builder);
    }
}

?>
