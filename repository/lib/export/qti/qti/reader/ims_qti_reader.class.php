<?php

/**
 * Helper class to read a QTI XML file.
 * Add a few helper methods.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class ImsQtiReader extends ImsXmlReader{
	
    public function __construct($item='', $return_null=false){
    	parent::__construct($item, $return_null);
    }

	public function list_interactions(){
		$names = Qti::get_interactions();
		$path = array();
		foreach($names as $name){
			$path[] = './/def:' .$name;
		}
		$path = implode(' | ', $path);
		
		return $this->query($path);
	}
	
	public function get_by_id($id){
		$path = './/*[@identifier="'. $id .'"]';
		$results = $this->query($path, $this->get_root());
		if(empty($results)){
			return $this->get_default_result();
		}else{
			return $results[0];
		}
	}
	
	public function get_child_by_id($id){
		$path = './/*[@identifier="'. $id .'"]';
		$results = $this->query($path);
		if(empty($results)){
			return $this->get_default_result();
		}else{
			return $results[0];
		}
	}
	
	public function is_feedback(){
		return Qti::is_feedback($this->name());
	}
}