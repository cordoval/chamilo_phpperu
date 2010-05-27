<?php
require_once dirname(__FILE__) . '/peer_assessment_display_component.class.php';

class PeerAssessmentDisplay extends ComplexDisplay
{
	const ACTION_TAKE_PEER_ASSESSMENT = 'take_publication';
    const ACTION_VIEW_PEER_ASSESSMENT = 'view_publication_results';
    
    function run()
    {
        $component = parent :: run();
        
        if (! $component)
        {
            $action = $this->get_action();
            
            switch ($action)
            {
            	case self :: ACTION_TAKE_PEER_ASSESSMENT :
                    $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
                    break;
                case self :: ACTION_VIEW_PEER_ASSESSMENT :
                    $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentResultViewer', $this);
                    break;
                default :
                    $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
            }
        }
        
        return $component->run();
    }
    
    function get_current_attempt_id()
    {
        return $this->get_parent()->get_current_attempt_id();
    }

	function get_application_component_path ()
	{
		return dirname(__FILE__) . '/component/';
	}

    
}
?>