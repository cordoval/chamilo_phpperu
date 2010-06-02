<?php

require_once dirname(dirname(__FILE__)) .'/main.php';

/**
 * Used to translate paths/URLs that need to be renamed during exportation.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiExportResourceManager extends QtiResourceManagerBase{
	
	public function __construct($target_root){
		parent::__construct('', $target_root);
	}
	
	public function is_url_locale($path){
		$path = str_replace('//', '/', $path);
		$pieces = explode('/', $path);
		if(count($pieces)<2){
			return true;
		}
		$protocol = $pieces[0];
		if($protocol == 'http' || $protocol == 'https'){
			return false;
		}
		$host = strtolower($pieces[1]);
		$localhost = strtolower($_SERVER['SERVER_NAME']);
		return $host = '127.0.0.1' || $host = 'localhost' || $host = $localhost;
	}
	
	public function url_basename($path){
		$pieces = explode('/', $path);
		return count($pieces)==0 ? $path : $pieces[count($pieces)-1];
	}

    public function translate_path($path){
    	if(!$this->is_url_locale($path)){
    		return $path;
    	}
    	
    	$basename = $this->url_basename($path);
    	$result = $this->get_target_root() . $basename;
    	debug($basename. ' '. $result);
    	$this->register($result, $path);
    	return $result;
    }
    
}


