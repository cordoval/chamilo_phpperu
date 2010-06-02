<?php
require_once dirname(__FILE__) .'/question_builder.class.php';

/**
 * Buffered factory. Will only import each file once.
 * If the same file is imported twice  then the second time the object id is returned from the cache.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class BufferedFactory{

	private $cache = array();
    
    public function import($path, $user, $category, Log $log){
    	$key = strtolower($path);
    	if(isset($this->cache[$key])){
    		return $this->cache[$key];
    	}else if($importer = BuilderImport::factory($path, $user, $category, $this, $log)){
    		$result = $importer->import_content_object();
    		$this->cache[$key] = $result;
    		return $result;
    	}else{
    		$file_name = basename($path);
    		$log->write(Translation::translate('ContentObjectNotImported'). ': ' .$file_name, Log::TYPE_ERROR);
    		return null;
    	}
    }
    
    public function get_cache(){
    	return $this->cache;
    }
    
}
?>