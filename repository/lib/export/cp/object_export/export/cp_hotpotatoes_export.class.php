<?php

/**
 * Export Hotpotatoes objects. Write attachment. Do not export object's properties.
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpHotpotatoesExport extends CpObjectExport{

	public static function factory($settings){
		$object = $settings->get_object();
		if(self::accept($object)){
			return new self($settings);
		}else{
			return NULL;
		}
	}
	
	public static function accept($object){
		if(! $object instanceof ContentObject){
			return false;
		}
		return $object instanceof Hotpotatoes || $object->get_type() == Hotpotatoes::get_type_name();
	}
	
	public function export_content_object(){
		$settings = $this->get_settings();
		$object = $settings->get_object();
		
        $path = $object->get_path();
    	if(empty($path)){
    		return false;
    	}
    	$filename = basename($path);
    	$parts = explode('.', $filename);
    	$ext = count($parts) > 1 ? end($parts) : '';
    	$href = $this->get_file_name($object, $ext);
    	$directory = $settings->get_directory();
    	if(! Filesystem::copy_file($path, $to_path = $directory.$href, true)){
    		return false;
    	}
    	
		$this->add_manifest_entry($object, $href);
    	return $directory.$href;
	}
	
}


?>