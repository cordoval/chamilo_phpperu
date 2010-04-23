<?php
abstract class StreamingMediaComponent extends SubManager
{
	const BROWSER_COMPONENT = 'browser';
	const CREATOR_COMPONENT = 'creator';
	const DOWNLOADER_COMPONENT = 'downloader';
	const EXPORTER_COMPONENT = 'exporter';
	const IMPORTER_COMPONENT = 'importer';
	const VIEWER_COMPONENT = 'viewer';
	
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
}
?>