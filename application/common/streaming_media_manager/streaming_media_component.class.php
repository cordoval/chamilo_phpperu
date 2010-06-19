<?php
abstract class StreamingMediaComponent extends SubManager
{
	const BROWSER_COMPONENT = 'browser';
	const CREATOR_COMPONENT = 'creator';
	const DOWNLOADER_COMPONENT = 'downloader';
	const EXPORTER_COMPONENT = 'exporter';
	const IMPORTER_COMPONENT = 'importer';
	const VIEWER_COMPONENT = 'viewer';
	const SELECTER_COMPONENT = 'selecter';
	const EDITOR_COMPONENT = 'editor';
	const DELETER_COMPONENT = 'deleter';
	
	static function factory($type, $application)
	{
		$file = dirname(__FILE__) . '/component/' . $type . '.class.php';
    	if(!file_exists($file))
    	{
    		throw new Exception(Translation :: get('StreamingMediaComponentTypeDoesNotExist', array('type' => $type)));
    	}
    	
    	require_once $file;
    	
    	$class = 'StreamingMedia' . Utilities :: underscores_to_camelcase($type) . 'Component';
    	return new $class($application);
	}
	
	function get_application_component_path()
	{
		
	}
	
	function get_streaming_media_object_viewing_url($object)
	{
		return $this->get_parent()->get_streaming_media_object_viewing_url($object);
	}
	
	function count_streaming_media_objects($condition)
	{
		return $this->get_parent()->count_streaming_media_objects($condition);
	}
	
	function retrieve_streaming_media_objects($condition, $order_property, $offset, $count)
	{
		return $this->get_parent()->retrieve_streaming_media_objects($condition, $order_property, $offset, $count);
	}
	
	function retrieve_streaming_media_object($id)
	{
		return $this->get_parent()->retrieve_streaming_media_object($id);
	}
	
	function delete_streaming_media_object($id)
	{
		return $this->get_parent()->delete_streaming_media_object($id);
	}
	
	function export_streaming_media_object($object)
	{
		return $this->get_parent()->export_streaming_media_object($object);
	}
	
	function get_property_model()
	{
		return $this->get_parent()->get_property_model();
	}
	
	function support_sorting_direction()
	{
		return $this->get_parent()->support_sorting_direction();
	}
	
	function translate_search_query($query)
	{
		return $this->get_parent()->translate_search_query($query);
	}
	
	function get_menu_items()
	{
		return $this->get_parent()->get_menu_items();
	}
}
?>