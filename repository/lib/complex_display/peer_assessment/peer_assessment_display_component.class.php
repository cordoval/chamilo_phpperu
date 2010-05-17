<?php
/**
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class PeerAssessmentDisplayComponent extends ComplexDisplayComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('PeerAssessment', $component_name, $builder);
    }
    
    function get_current_attempt_id()
    {
        return $this->get_parent()->get_current_attempt_id();
    }
}

?>