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

    function save_answer($complex_question_id, $answer)
    {
        return $this->get_parent()->save_answer($complex_question_id, $answer);
    }

    function finish_peer_assessment($percent)
    {
        return $this->get_parent()->finish_peer_assessment($percent);
    }
    
    function get_current_attempt_id()
    {
        return $this->get_parent()->get_current_attempt_id();
    }

    function get_go_back_url()
    {
        return $this->get_parent()->get_go_back_url();
    }

    function parse($value)
    {
        return $this->get_parent()->parse($value);
    }
	 
}

?>