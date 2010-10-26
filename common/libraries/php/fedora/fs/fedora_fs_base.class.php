<?php
namespace common\libraries;

/**
 * Base class for FS - file system - objects.
 * FS objects are used to provide different ways to navigate Fedora objects. For example queries, collections, object, datastream, etc.
 * To be used with the Fedora repository API to build the user interface.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class fedora_fs_base{

	protected static $max_results = 250;

	public static function get_max_results(){
		if(isset(self::$max_results)){
			return self::$max_results;
		}else{
			return 250;
		}
	}

	public static function set_max_results($value){
		self::$max_results = $value;
	}

	public function __construct($fsid = ''){
		$this->set('fsid', $fsid);
	}

	public function get_thumbnail(){
		return $this->get(__FUNCTION__, '');
	}

	public function set_thumbnail($value){
		$this->set(__FUNCTION__, $value);
	}

	/**
	 * Used to identify a file system - folder, query, object, datastream, etc - object.
	 *
	 */
	public function get_fsid(){
		return $this->get(__FUNCTION__, '');
	}

	public function set_fsid($value){
		$this->set(__FUNCTION__, $value);
	}

	public function get_title(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_shorttitle(){
		return $this->get(__FUNCTION__, $this->get_title());
	}

	public function get_modified_date(){
		return $this->get(__FUNCTION__, 0);
	}

	public function get_created_date(){
		return $this->get(__FUNCTION__, 0);
	}

	public function get_owner(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_size(){
		return $this->get(__FUNCTION__, 0);
	}

	public function get_source(){
		return $this->get(__FUNCTION__, '');
	}

	/**
	 * @return The html class to be used for this object
	 */
	public function get_class(){
		return $this->get(__FUNCTION__, '');
	}

	public function get_path($path=array()){
		$result = $path;
		$result[] = $this;
		$result = serialize($result);
		return $result;
	}

	public function is_system(){
		return false;
	}

	public function format($path = array()){
		$title = $this->get_title();
		if($title){
			$result = array(
		        		'title' => $title,
						'shorttitle' => $title,
		        		'date'=> $this->get_date(),
		        		'size'=> $this->get_size(),
		        		'source'=> $this->get_source(),
		        		'thumbnail' => $this->get_thumbnail(),
						'path' => $this->get_path($path),
			);
		}else{
			$result = array();
		}
		return $result;
	}

	/**
	 *
	 * @param FedoraProxy $fedora
	 * @return array()
	 */
	public function query(FedoraProxy $fedora, $sort=false, $limit=false, $offset=false){
		return array();
	}

	public function count(FedoraProxy $fedora){
		return count($this->query>($fedora));
	}

	/**
	 * Locate a child fs object based on his fs id.
	 *
	 * @param string $id
	 * @return fedora_fs_base
	 */
	public function find($fsid){
		return $fsid == $this->get_fsid() ? $this : false;
	}

	public function get($name, $default=NULL){
		$name = str_replace('get_', '', $name);
		if(isset($this->$name)){
			return $this->$name;
		}else{
			return $default;
		}
	}

	public function set($name, $value=NULL){
		$name = str_replace('set_', '', $name);
		if($value){
			$this->$name = $value;
		}else{
			unset($this->name);
		}
	}

	public function __call($name, $args){
		$parts = explode('_', $name, 2);
		$action = reset($parts);
		$name = end($parts);
		if($action == 'get'){
			return $this->get($name);
		}else if($action == 'set'){
			return $this->set($name, reset($args));
		}else{
			return false;
		}
	}

	public static function __callstatic($name, $args){
		$parts = explode('_', $name, 2);
		$action = reset($parts);
		$name = end($parts);
		if($action == 'get'){
			if(isset(self::$name)){
				return self::$name;
			}else{
				return NULL;
			}
		}else if($action == 'set'){
			self::$name = reset($args);
		}
	}

	protected function translate($key){
		return fedora_fs_translate($key, 'repository_fedora');
	}

}

