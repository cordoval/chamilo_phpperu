<?php
abstract class StreamingMediaManager extends SubManager
{
	const PARAM_STREAMING_MEDIA_MANAGER_ACTION = 'streaming_action';
	
	const ACTION_VIEW_STREAMING_MEDIA = 'view';
	const ACTION_EXPORT_STREAMING_MEDIA = 'export';
	const ACTION_IMPORT_STREAMING_MEDIA = 'import';
	const ACTION_BROWSE_STREAMING_MEDIA = 'browse';
	const ACTION_DOWNLOAD_STREAMING_MEDIA = 'download';
	const ACTION_UPLOAD_STREAMING_MEDIA = 'upload';
	
	const PARAM_STREAMING_MEDIA_ID = 'streaming_media_id';
	const CLASS_NAME = __CLASS__;
	
	function StreamingMediaManager($application)
	{
		parent :: __construct($application);
		
		$streaming_media_manager_action = Request :: get(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
        if ($streaming_media_manager_action)
        {
            $this->set_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION, $streaming_media_manager_action);
        }
	}
	
	static function factory($type, $application)
	{
		$file = dirname(__FILE__) . '/type/' . $type . '/' . $type . '_streaming_media_manager.class.php';
    	if(!file_exists($file))
    	{
    		throw new Exception(Translation :: get('StreamingMediaManagerTypeDoesNotExist', array('type' => $type)));
    	}
    	
    	require_once $file;
    	
    	$class = Utilities :: underscores_to_camelcase($type) . 'StreamingMediaManager';
    	return new $class($application);
	}
	
	function is_ready_to_be_used()
    {
//        $action = $this->get_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
//
//        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
		return false;
    }
    
	function any_object_selected()
    {
        //$object = Request :: get(self :: PARAM_ID);
        //return isset($object);
    }
	
	function get_application_component_path()
	{
		return Path :: get_application_library_path() . 'streaming_media_manager/component/';
	}
	
	function get_action()
    {
        return $this->get_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
    }
	
	function display_header()
	{
		$action = $this->get_action();
		parent :: display_header();
		
		$html = array();
		$html[] = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        $streaming_media_actions = $this->get_streaming_medi_actions();
        
		if($action == self :: ACTION_VIEW_STREAMING_MEDIA)
        {
        	$streaming_media_actions[] = self :: ACTION_VIEW_STREAMING_MEDIA;
        }
        
        foreach ($streaming_media_actions as $streaming_media_action)
        {
            $html[] = '<li><a';
            if ($action == $streaming_media_action)
            {
                $html[] = ' class="current"';
            }
//            elseif (($action == self :: ACTION_PUBLISHER || $action == 'multirepo_viewer') && $repo_viewer_action == self :: ACTION_CREATOR)
//            {
//                $html[] = ' class="current"';
//            }

            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = $streaming_media_action;
            
	        if($streaming_media_action == self :: ACTION_VIEW_STREAMING_MEDIA)
	        {
	        	$parameters[self :: PARAM_STREAMING_MEDIA_ID] = Request :: get(self :: PARAM_STREAMING_MEDIA_ID);
	        }
            
            $html[] = ' href="' . $this->get_url($parameters, true) . '">' . htmlentities(Translation :: get(ucfirst($streaming_media_action) . 'Title')) . '</a></li>';
        }
        $html[] = '</ul><div class="tabbed-pane-content">';

        echo implode("\n", $html);
	}
	
	function get_streaming_medi_actions()
	{
		return array(self :: ACTION_BROWSE_STREAMING_MEDIA, self :: ACTION_UPLOAD_STREAMING_MEDIA);
	}
	
	function display_footer()
	{
		echo('</div></div>');
		parent :: display_footer();
	}
	
	abstract function count_streaming_media_objects($condition);
	
	abstract function retrieve_streaming_media_objects($condition, $order_property, $offset, $count);
	
}
?>