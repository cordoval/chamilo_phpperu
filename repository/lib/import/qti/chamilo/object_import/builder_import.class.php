<?php
require_once dirname(__FILE__) .'/question_builder.class.php';

/**
 * Adapter between builders and importers.
 * Builders are responsible to construct a chamilo question object.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class BuilderImport extends QtiImport{

    public static function factory($path, $user, $category, $factory, $log){
    	if(! Qti::is_qti_file($path)){
    		return null;
    	}
  		$reader = new ImsQtiReader($path, false);
  		$item = $reader->get_root(); //query('/def:assessmentItem');
  		$directory = dirname($path);
  		$base_url = ''; //not used:
        if($builder = BuilderBase::factory($item, $directory, $base_url, $category, $user, $factory, $log)){
        	return new self($path, $user, $category, $builder, $log);
        }else{
        	return null;
        }
    }

    private $builder = null;
    private $log = null;
    
    public function __construct($path, $user, $category, $builder, $log){
    	parent::__construct($path, $user, $category);
    	$this->builder = $builder;
    	$this->log = $log;	
    }
    
    function import_content_object(){
    	$result = false;
    	$path = $this->get_content_object_file();
  		$reader = new ImsQtiReader($path, false);
  		$item = $reader->get_root();
        $object = $this->builder->build($item);
        $result = $this->save_content_object($object);
  		return $result;
    }
    
    protected function save_content_object($object){
        $object->set_owner_id($this->get_user()->get_id());
        $object->set_parent_id($this->get_category());
        
        $id = $object->get_id();
        if(empty($id)){
        	$object->create();
        }else{
        	$object->update();
        }
        return $object->get_id();
    }
    
}
?>