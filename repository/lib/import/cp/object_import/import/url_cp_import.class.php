<?php

require_once Path::get_repository_path() .'lib/import/qti/main.php';

/**
 * Import url files as LINK objects.  
 * 
 * @copyright (c) 2010 University of Geneva 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class UrlCpImport extends CpObjectImportBase{

	public function get_weight(){
		return 100;
	}
		
	protected function process_import(ObjectImportSettings $settings){
    	if($content = file_get_contents($settings->get_path())){
    		$result = new Link();
    		$result->set_url($this->get_url($content));
			$this->save($settings, $result);
    		return $result;
    	}else{
    		return false;
    	}
    }
    
    protected function get_url($content){
    	$lines = explode("\n", $content);
    	foreach($lines as $line){
    		$parts = explode('=', $line);
    		if(count($parts)>1 && strtolower($parts[0]) == 'url'){
    			return $parts[1];
    		}
    	}
    	return '';
    }
    
    
    
}






?>