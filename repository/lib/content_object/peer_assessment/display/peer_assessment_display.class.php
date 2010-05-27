<?php

class PeerAssessmentDisplay extends ComplexDisplay
{
	const ACTION_TAKE_PEER_ASSESSMENT = 'take_publication';
    const ACTION_VIEW_PEER_ASSESSMENT = 'view_publication_results';
    
    function run()
    {
        $action = $this->get_action();
            
        switch ($action)
        {
            case self :: ACTION_TAKE_PEER_ASSESSMENT :
                    $component = $this->create_component('PeerAssessmentViewer');
                    break;
            case self :: ACTION_VIEW_PEER_ASSESSMENT :
                    $component = $this->create_component('PeerAssessmentResultViewer');
                    break;
                default :
                    $component = $this->create_component('PeerAssessmentViewer');
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