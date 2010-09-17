<?php

require_once dirname(dirname(__FILE__)) .'/main.php';

/**
 * Base class used to translate urls/paths and to keep track of external ressources which need to be copied alongside the question.
 * I.e. images, objects, etc.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiResourceManagerBase{
	
	private $target_root = '';
	private $source_root = '';
	private $current_path = '';
	private $resources = array();
	
	public function __construct($source_root, $target_root){
		$this->set_source_root($source_root);
		$this->set_target_root($target_root);
	}
	
	public function get_target_root(){
		return $this->target_root;
	}
	
	public function set_target_root($value){
		$value = empty($value) ? '' : rtrim($value, '/') .'/';
		$this->target_root = $value;
	}
	
	public function get_source_root(){
		return $this->source_root;
	}
	
	public function set_source_root($value){
		$value = empty($value) ? '' : rtrim($value, '/') .'/';
		$this->source_root = $value;
	}

	public function get_current_path(){
		return $this->current_path;
	}
	
	public function set_current_path($value){
		$value = empty($value) ? '' : $value;
		$this->current_path = $value;
	}
	
	public function get_current_directory(){
		$path = $this->get_current_path();
		$path = empty($path) ? '' : $path;
		if(is_file($path)){
			return dirname($path) .'/';
		}else{
			return rtrim($path, '/') .'/';
		}
	}
	
	public function get_resources(){
		return $this->resources;
	}
	
	public function reset_resources(){
		$this->resources = array();
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
    
    public function is_path_relative($path){
    	return strlen($path)<5 || strtolower(substr($path, 0, 4)) != 'http';
    }
    
    protected function canonicalize($address){
	    $result = explode('/', $address);
	    $keys = array_keys($result, '..');
	
	    foreach($keys AS $keypos => $key){
	        array_splice($result, $key - ($keypos * 2 + 1), 2);
	    }
	
	    $result = implode('/', $result);
	    $result = str_replace('./', '', $result);
	    return $result;
	}
    
	public function register($translated_path, $original_path){
    	$this->resources[$translated_path] = $original_path;
		
	}
}














	
?>