<?php

/**
 * Object cache implemented as dictionary where the key can be an object.
 *
 *
 * @copyright (c) 2010 University of Geneva
 *
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class ObjectCache{

	private $cache = array();

	public function get_all(){
		return $this->cache;
	}

	public function has($item){
		$key = $this->get_key($item);
		return isset($this->cache[$key]);
	}

	public function is_registered($item){
		$key = $this->get_key($item);
		return array_key_exists ($key, $this->cache);
	}

	public function register($item, $value){
		$key = $this->get_key($item);
		$this->cache[$key] = $value;
	}

	public function unregister($item){
		$key = $this->get_key($item);
		unset($this->cache[$key]);
	}

	public function get($item){
		$key = $this->get_key($item);
		if($this->is_registered($key)){
			return $this->cache[$key];
		}else{
			return null;
		}
	}

	public function clear(){
		$this->cache = array();
	}

	protected function get_key($item){
		if(is_object($item)){
			$result = CpExport::get_object_name($item);
		}else{
			$result = $item;
		}
		return $result;
	}

}












?>