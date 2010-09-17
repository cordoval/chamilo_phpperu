<?php
require_once dirname(__FILE__) .'/question_builder.class.php';

/**
 * Adapter between builders and importers.
 * Builders are responsible to construct a chamilo question object.
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class BuilderImport extends QtiImport{

    public static function factory(ObjectImportSettings $settings){
    	$path = $settings->get_path();
    	if(! Qti::is_qti_file($path)){
    		return null;
    	}
  		$reader = new ImsQtiReader($path, false);
  		$item = $reader->get_root();
        if($builder = BuilderBase::factory($item, $settings)){
        	return new self($settings, $builder);
        }else{
        	return null;
        }
    }

    private $builder = null;
    private $settings = null;

    public function __construct(ObjectImportSettings $settings, $builder){
    	parent::__construct($settings->get_path(), $settings->get_user(), $settings->get_category_id());
    	$this->builder = $builder;
    	$this->settings = $settings;
    }

    public function get_settings(){
    	return $this->settings;
    }

    public function get_builder(){
    	return $this->builder;
    }

    public function import_content_object(){
    	$result = false;
    	$path = $this->get_content_object_file();
  		$reader = new ImsQtiReader($path, false);
  		$item = $reader->get_root();
        $object = $this->builder->build($item);
        return $this->save_content_object($object);
    }

    protected function save_content_object(ContentObject $object){
        $object->set_owner_id($this->get_user()->get_id());
        $object->set_parent_id($this->get_category_id());

     	if(!$object->save()){
        	$this->get_log()->error($object->get_errors());
        	return null;
        }else{
        	return $object; //->get_id()
        }
    }

}







?>