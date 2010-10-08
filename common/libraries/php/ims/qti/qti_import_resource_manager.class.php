<?php

require_once dirname(__FILE__) . '/qti_resource_manager_base.class.php';

/**
 * Class used to translate urls/paths and to keep track of external ressources which need to be copied alongside the question.
 * I.e. images, objects, etc.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiImportResourceManager extends QtiResourceManagerBase{
	
	public function __construct($source_root, $target_root){
		parent::__construct($source_root, $target_root);
	}
	
    public function translate_path($path){
    	if(!$this->is_path_relative($path)){
    		return $path;
    	}
    	
    	$source_path = $this->get_current_directory() . $path;
    	$source_path = $this->canonicalize($source_path);
    	
    	$target_root = $this->get_target_root();
    	$source_root = $this->canonicalize($this->get_source_root());
    	
    	$result = str_replace($source_root, $target_root, $source_path);
    	$this->register($result, $source_path);
    	return $result;
    }
}



















	
?>