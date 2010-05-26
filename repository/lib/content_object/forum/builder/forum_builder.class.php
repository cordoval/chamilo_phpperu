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
            case ComplexBuilder :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECT :
                $component = ForumBuilderComponent :: factory('Browser', $this);
                break;
            case ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = ForumBuilderComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_STICKY_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = ForumBuilderComponent :: factory('Sticky', $this);
                break;
            case self :: ACTION_IMPORTANT_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = ForumBuilderComponent :: factory('Important', $this);
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
}

?>