<?php

class BufferedObjectExport{
	
	private $export = null;
	private $settings = null;
	
	public function __construct($settings, $export){
		$this->settings = $settings;
		$this->export = $export;
	}
	
	public function get_export(){
		return $this->export;
	}
	
	/**
	 * @return ObjectExportSettings
	 */
	public function get_settings(){
		return $this->settings;
	}
	
	public function export_content_object(){
		$export = $this->get_export();
		$settings = $this->get_settings();
    	$cache = $settings->get_cache();
    	$object = $settings->get_object();
    	if($result = $cache->get($object)){
    		return $result;
    	}else {
    		//must register object before export for circular references
    		$href = CpExport::get_object_file_name($object);
    		$cache->register($object, $href);
    		if($result = $export->export_content_object()){
	    		$cache->register($object, $result);
	    		return $result;
	    	}else{
    			$object_name = CpExport::get_object_name($object);
    			$message = Translation::translate('ContentObjectNotExported'). ': ' .$object_name;
    			$log = $settings->get_log();
	    		$log->error($message);
	    		return null;
	    	}
    	}
	}
	
	
}






?>