<?php
/*
class PropertyBag
{	
    private $values = array();
    
    public static function property_names(){
    	$reflection = new ReflectionClass(__CLASS__);
    	return array_keys($reflection->getConstants());
    }
    
    public function __construct($item=null){
    	if(!is_null($item)){
    		$this->set($item);
    	}
    }
    
    public function get($property_name){
    	return $this->values[$property_name];
    }
    
    public function set($property_name, $value){
    	$this->values[$property_name] = $value;
    }

    /**
     * Default - i.e. set if empty - the property
     * @param unknown_type $item
     * @param unknown_type $value
     */
    /*public function def($item, $value=''){
    	if($item instanceof PropertyBag){
    		$this->def_bag($item);
    	}else if(is_string($item)){
    		$this->def_property($item, $value);
    	}else if(is_object($item)){
    		$this->def_object($item);
    	}else{
    		throw new Exception('Not implemented');
    	}
    }
    
    public function def_property($property_name, $value){
    	$values = $this->values;
    	if(!isset($values[$property_name]) || empty($values[$property_name])){
    		$this->set($property_name, $value);
    	}
    }
    
    public function def_object($object){
    	foreach($object as $name=>$value){
    		$this->def($name, $value);
    	}
    }
    
    public function def_bag($bag){
    	$bag_properties = $bag->get_properties();
    	foreach($bag_properties as $name=>$value){
    		$this->def($name, $value);
    	}
    }
    
    public function get_properties(){
    	return $this->values;
    }
    
    public function __call($name, $arguments){
    	$n = explode('_', $name);
    	$action = $n[0];
    	$property_name = $n[1];
    	if($action=='get'){
    		return $this->get($property_name);
    	}else if($action == 'set'){
    		$this->set($property_name, $arguments[0]);
    	}else if($action == 'def'){
    		$this->def($property_name, $arguments[0]);
    	}
    }
}
*/

?>