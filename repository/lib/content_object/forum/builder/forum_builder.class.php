<?php
/**
 * $Id: forum_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum
 */

class ForumBuilder extends ComplexBuilder
{
    const ACTION_STICKY_COMPLEX_CONTENT_OBJECT_ITEM = 'sticky_complex_content_object_item';
    const ACTION_IMPORTANT_COMPLEX_CONTENT_OBJECT_ITEM = 'important_complex_content_object_item';
	
    function ForumBuilder($parent)
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
            case ComplexBuilder :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Creator');
                break;
            case ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM : 
            	$component = $this->create_component('Deleter');
                break;
            case ComplexBuilder :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM : 
            	$component = $this->create_component('Mover');
                break;
            case ComplexBuilder :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM : 
            	$component = $this->create_component('Updater');
                break;
            case ComplexBuilder :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM : 
            	$component = $this->create_component('Viewer');
                break;
            case ComplexBuilder :: ACTION_CHANGE_PARENT : 
            	$component = $this->create_component('ParentChanger');
                break;
            case self :: ACTION_STICKY_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Sticky');
                break;
            case self :: ACTION_IMPORTANT_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Important');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }
    
	protected function parse_input_from_table($action)
    {
        if ($action)
        {
            switch ($action)
            {
                case self :: PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM . '_forum_table' :
		            $selected_ids = $_POST['forum_table' . ObjectTable :: CHECKBOX_NAME_SUFFIX];
                    break;
                case self :: PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM . '_topic_table' :
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
            
            $this->set_action(self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM);
            Request :: set_get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $selected_ids);
        }
    }

    function get_complex_content_object_item_sticky_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_STICKY_COMPLEX_CONTENT_OBJECT_ITEM, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item()->get_id(), self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id()));
    }

    function get_complex_content_object_item_important_url($complex_content_object_item, $root_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_IMPORTANT_COMPLEX_CONTENT_OBJECT_ITEM, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item()->get_id(), self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id()));
    }
 
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}

?>