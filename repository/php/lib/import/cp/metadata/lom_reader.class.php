<?php

class ImsLomReader extends  ImsMetadataReader
{
	
	const LANG_NONE = 'x-none';

    function __construct($item = '')
    {
    	parent::__construct($item);
    }

    public static function get_schema(){
    	return 'lom';
    }
    
    public static function get_schema_version(){
    	return '1.0';
    }
    
    public static function is_compatible($schema, $schemaversion){
    	return self::get_schema() == $schema && self::get_schema_version()==$schemaversion;
    }
    
    public function to_metadata_object(){
    	$result = new ImscpObjectMetadata();
    	
    	$general = $this->get_general();
    	$idenfiers = $general->get_identifiers();
    	$ids = array();
    	foreach($idenfiers as $identifier){
    		$catalog = $identifier->get_catalog();
    		$entry = $identifier->get_entry();
    		$ids[$catalog] = $entry;
    	}
    	
    	$title = $general->get_title();
    	$description = $general->get_description();
    	
    	$result->def_description($description);
    	$result->def_title($title);
    	$result->def_ids($ids);
    	return $result;
    }
    
    public function get_general(){
    	return $this->first('/imsmd:lom/imsmd:general');
    }
    
    public function get_identifiers(){
    	return $this->query('.//imsmd:identifier');
    }
    
    public function get_catalog(){
    	$item = $this->first('.//imsmd:catalog');
    	return $item->get_lang_value();
    }

    public function get_entry(){
    	$item = $this->first('.//imsmd:entry');
    	return $item->get_lang_value();
    }
    
    public function get_title($lang=''){
    	$title = $this->first('.//imsmd:title');
    	return $title->get_lang_value($lang);
    }
    
    public function get_description($lang=''){
    	$title = $this->first('.//imsmd:description');
    	return $this->get_lang_value($lang);
    }
    
    public function get_lang_value($lang=''){
    	$from = $this->get_current();
    	$lang = strtolower($lang);
    	if(empty($lang))
    		$lang = self::LANG_NONE;
    	$children = $from->childNodes;
    	foreach($children as $child){
    		if(!($child instanceof DOMText)){
	    		$child_language = $child->getAttribute('lang');
	    		if(empty($child_language)){
	    			$child_language = $child->getAttribute('language');
	    		}
	    		$child_language = strtolower($child_language);
	    		if($lang == $child_language){
	    			return $child->nodeValue;
	    		}
    		}
    	}
    	$lang = self::LANG_NONE;
    	foreach($children as $child){
            if(!($child instanceof DOMText)){
	    		$child_language = $child->getAttribute('lang');
	    		if(empty($child_language)){
	    			$child_language = $child->getAttribute('language');
	    		}
	    		$child_language = strtolower($child_language);
	    		if($lang == $child_language){
	    			return $child->nodeValue;
	    		}
    	   }
    	}
    	foreach($children as $child){
    		return $child->nodeValue;
    	}
    	return $form->nodeValue;
    }
    
}
?>