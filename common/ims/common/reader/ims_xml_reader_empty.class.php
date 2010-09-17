<?php

require_once  dirname(__FILE__) . '/ims_xml_reader.class.php';

/**
 * Empty object pattern for xml reader.
 * Either does nothing or returns expected default values;
 *   
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class ImsXmlReaderEmpty extends ImsXmlReader
{
    
    public function __construct(){
    	parent::__construct('', true);
    }
    
    public function copy($current){
    	return $this;
    }
    
    public function load($item){
    	return false;
    }
    
    public function load_node($node){
    	return false;
    }
    
    public function load_path($path){
    	return false;
    }
    
    public function get_default_namespace(){
    	return '';
    }
    
    public function get_root(){
    	return $this;
    }
    
    public function get_xml(){
    	return '';
    }
    
    public function get_return_null(){
    	return false;
    }
    
    public function set_return_null($value){
    	throw new Exception('Invalid operation');
    }
    
    public function get_default_result(){
    	return $this;
    }
    
    public function is_empty(){
    	return true;
    }
    
    public function query($path, $context=null){
    	return array();
    }
    
    public function first($path, $context=null){
    	return self::get_empty_reader();
    }
    
    public function evaluate($query, $context=null){
    	return self::get_empty_reader();
    }

    public function children_head(){
    	return $this;
    }
    
	public function children(){
		return array();
	}    
    
    public function value($name=''){
    	return '';
    }
    
    public function get($name){
    	return self::get_empty_reader();
    } 
    
    public function is_scalar(){
    	return false;
    }
    
    public function get_attribute($name){
		return '';
    }
    
    public function is($name){
    	return false;
    }
    
    public function has($name){
    	return false;
    }

    public function all($name){
    		return array();
    }
    
    public function getIterator() {
        return new IteratorEmpty();
    }

    public function text(){
    	return '';
    } 
    
    public function name(){
    	return '';
    }
}


/**
 * Empty object pattern for Iterator. I.e. an iterator over an empty collection.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class IteratorEmpty implements Iterator
{
    public function rewind() {
    	//do nothing
    }
    public function current() {
    	return null;
    }

    public function key() {
    	return null;
    }

    public function next() {
    	return null;
    }

    public function valid() {
    	return false;
    }

}








?>