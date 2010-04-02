<?php
require_once dirname(__FILE__) . '/peer_assessment_builder_component.class.php';

class PeerAssessmentBuilder extends ComplexBuilder
{
    const ACTION_STICKY_CLOI = 'sticky_cloi';
    const ACTION_IMPORTANT_CLOI = 'important_cloi';
	
    function PeerAssessmentBuilder($parent)
    {
    	$action = Request :: post('action');
    	$_POST['action'] = null;
    	parent :: __construct($parent);
    	$this->parse_input_from_table($action);
    }
    
    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = PeerAssessmentBuilderComponent :: factory('Browser', $this);
                break;
            case ComplexBuilder :: ACTION_CREATE_CLOI :
                $component = PeerAssessmentBuilderComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_STICKY_CLOI :
                $component = PeerAssessmentBuilderComponent :: factory('Sticky', $this);
                break;
            case self :: ACTION_IMPORTANT_CLOI :
                $component = PeerAssessmentBuilderComponent :: factory('Important', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }
    
	private function parse_input_from_table($action)
    {
        if ($action)
        {
            switch ($action)
            {
                case self :: PARAM_DELETE_SELECTED_CLOI . '_peer_assessment_table' :
		            $selected_ids = $_POST['peer_assessment_table' . ObjectTable :: CHECKBOX_NAME_SUFFIX];
                    break;
                case self :: PARAM_DELETE_SELECTED_CLOI . '_topic_table' :
		        	$selected_ids = $_POST['topic_table' . ObjectTable :: CHECKBOX_NAME_SUFFIX];
                    break;
            }
            
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            
            $this->set_action(self :: ACTION_DELETE_CLOI);
            Request :: set_get(self :: PARAM_SELECTED_CLOI_ID, $selected_ids);
        }
    }

    function get_complex_content_object_item_sticky_url($cloi, $root_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_STICKY_CLOI, self :: PARAM_ROOT_LO => $root_id, self :: PARAM_SELECTED_CLOI_ID => $cloi->get_id()));
    }

    function get_complex_content_object_item_important_url($cloi, $root_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_IMPORTANT_CLOI, self :: PARAM_ROOT_LO => $root_id, self :: PARAM_SELECTED_CLOI_ID => $cloi->get_id()));
    }
}

?>