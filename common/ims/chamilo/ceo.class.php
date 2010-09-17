<?php

/**
 * Ceo object's format. XML. One object per file.
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class Ceo{
	
	public static function is_ceo_file($path, $n1 = 'http://www.chamilo.org/xsd/ceo_v1p0'){
		if(strtolower(pathinfo($path, PATHINFO_EXTENSION)) != 'xml'){
			return false;
		}
		$basename = basename($path, '.xml');
		$basename = strtolower($basename);
		if($basename == 'imsmanifest'){
			return false;
		}
		$doc = new DOMDocument();
		$doc->load($path);
		$root = $doc->documentElement;
		if($root->tagName != 'export'){
			return false;
		}
		$namespace = $doc->documentElement->getAttribute('xmlns');
		$namespace = reset(explode(' ', $namespace));
		$ceo_namespaces = func_get_args();
		array_shift($ceo_namespaces);
		foreach($ceo_namespaces as $ceo_namespace){
			if(strtolower($ceo_namespace) == strtolower($namespace)){
				return true;
			}
		}
		return false;	
	}
	
	public static function is_ceo_content_object_file($path, $n1 = 'http://www.chamilo.org/xsd/ceo_v1p0'){
		if(!self::is_ceo_file($path, $n1)){
			return false;
		}
		
		$reader = new ImscpObjectReader($path, false);
		$type = strtolower($reader->get_objects()->get_object()->type);
		return $type != 'course';
	}

	public static function is_ceo_course_file($path, $n1 = 'http://www.chamilo.org/xsd/ceo_v1p0'){
			if(!self::is_ceo_file($path, $n1)){
			return false;
		}
		
		$reader = new ImscpObjectReader($path, false);
		$type = strtolower($reader->get_objects()->get_object()->type);
		return $type == 'course';
	}

    public function get_format_name(){
    	return 'ceo';
    } 
    
    public function get_format_version(){
    	return '1.0';
    }
    
    public function get_format_full_name(){
    	return 'ceo_v1p0';
    }

    public function get_namespace(){
    	return 'http://www.chamilo.org/xsd/ceo_v1p0';
    }

}