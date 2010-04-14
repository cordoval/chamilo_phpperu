<?php
require_once dirname(__FILE__) . '/peer_assessment_display_component.class.php';

class PeerAssessmentDisplay extends ComplexDisplay
{
    const ACTION_VIEW_PEER_ASSESSMENT = 'view';
    
    function run()
    {
        $component = parent :: run();
        
        if (! $component)
        {
            $action = $this->get_action();
            
            switch ($action)
            {
                case self :: ACTION_VIEW_PEER_ASSESSMENT :
                    $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
                    break;
                default :
                    $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
            }
        }
        
        return $component->run();
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