<?php

include_once dirname(__FILE__) . '/cp_object_import_base.class.php';

/**
 * Buffered import. Ensure that a file is imported only once in case of circular references.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class BufferedCpImport extends CpObjectImportBase{

	private $cache = array();
	private $current_key = '';
	private $child = NULL;
	

	public function __construct($child=NULL, $parent = NULL){
		parent::__construct($parent);
		$this->child = $child;
	}
	
	public function get_child(){
		return $this->child;
	}
	
	public function set_child($value){
		$this->child = $value;
	}	

	public function get_extentions(){
		return array('*');
	}

	public function accept($settings){
		$key = $this->get_key($settings);
		if($this->current_key == $key){
			return false;
		}
		
		return $this->is_cached($settings) || $this->get_child()->accept($settings);
	}

	public function get_weight(){
		return -1000000;
	}

	public function is_cached($settings){
		$key = $this->get_key($settings);
		return isset($this->cache[$key]);
	}
	
	public function get($settings){
		$key = $this->get_key($settings);
		if(isset($this->cache[$key])){
			return $this->cache[$key];
		}else{
			return NULL;
		}
	}
	
	protected function process_import(ObjectImportSettings $settings){
		if($result = $this->get($settings)){
			return $result;
		}

		$result = false;
		$key = $this->get_key($settings);
		$old_current_key = $this->current_key;
		try{
			$this->current_key = $key;
			$result = $this->get_child()->import($settings);
			$this->cache[$key] = $result;
			$this->current_key = $old_current_key;
		}catch(Exception $e){
			$this->current_key = $old_current_key;
			throw $e;
		}
		return $result;
	}

	protected function get_key($settings){
		$result = $settings->get_path();
		//we must normalize the string path to ensure it is unique.
		//calling the PHP standard function will not work as it only works with existing files and the algorythm delete files once imported
		$result = str_replace("\\", '/', $result);
		$result = str_replace('//', '/', $result);
		$result = str_replace('/./', '/', $result);
		$result = str_replace('/', DIRECTORY_SEPARATOR, $result);
		return $result;
	}

}







