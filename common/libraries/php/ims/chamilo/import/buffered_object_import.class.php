<?php

class BufferedObjectImport{

	private $import = null;

	/**
	 * @var ObjectImportSettings
	 */
	private $settings = null;

	public function __construct($settings, $import){
		$this->settings = $settings;
		$this->import = $import;
	}

	public function import_content_object(){
		$import= $this->import;
		$settings = $this->settings;
    	$cache = $settings->get_cache();
    	$log = $settings->get_log();
    	$path = $settings->get_path();
    	$key = $this->normalize_path($path); //needed, otherwise we can have // instead of / and the match will fail.

    	if($cache->is_registered($key)){
    		return $cache->get($key);
    	}else if($result = $import->import_content_object()){
    		$cache->register($key, $result);
    		return $result;
    	}else{
    		$cache->register($key, null);
    		$file_name = basename($path);
    		$log->error(Translation::translate('ContentObjectNotImported'). ': ' .$file_name);
    		return null;
    	}
	}

	/**
	 * Normalize a path to its default representation.
	 * Convert from windows to unix separators. Replaces //, ///, ... to /.
	 *
	 * @param string $path
	 * @return string
	 */
	protected function normalize_path($path){
		$result = $path;
		$result = str_replace("\\", '/', $result);
		$result = str_replace('//', '/', $result);
		$result = str_replace('//', '/', $result);
		$result = str_replace('//', '/', $result);
		return $result;
	}


}






?>