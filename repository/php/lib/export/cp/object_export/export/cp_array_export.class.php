<?php

/**
 * Export an array of objects. 
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpArrayExport extends CpObjectExport{

	public static function factory($settings){
		$object = $settings->get_object();
		if(self::accept($object)){
			return new self($settings);
		}else{
			return NULL;
		}
	}
	
	public static function accept($object){
		return is_array($object);;
	}	
	
	public function export_content_object(){
		$settings = $this->get_settings();
		$items = $settings->get_object();
		foreach($items as $item){
			$href = $this->export_child($item);
		}
	}

}


















?>