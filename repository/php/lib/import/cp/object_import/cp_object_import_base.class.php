<?php

/**
 *
 * Base class for Object Import. I.e. single file import per opposition to the CP multi file import.
 * Each subclass provides support for a specific format.
 *
 * @copyright (c) 2010 University of Geneva
 *
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpObjectImportBase{

	/**
	 * @return CpObjectImportBase
	 */
	public static function factory(){
		$result = new BufferedCpImport();
		$aggregate = new CpObjectImportAggregate($result);
		$result->set_child($aggregate);
		$directory = dirname(__FILE__) .'/import/';
		$files = scandir($directory);
		$files = array_diff($files, array('.', '..'));
		foreach($files as $file){
			$path = $directory.$file;
			if(strpos($file, '.class.php') !== false){
				include_once($path);
				$class = str_replace('.class.php', '', $file);
				$class = Utilities::underscores_to_camelcase($class);
				$importer = new $class($aggregate);
				$aggregate->add($importer);
			}
		}
		$aggregate->sort();
		return $result;
	}

	private $parent;

	public function __construct($parent = null){
		$this->parent = $parent;
	}

	/**
	 * Direct parent of the current object. Must be an aggregated importer.
	 */
	public function get_parent(){
		return $this->parent;
	}

	/**
	 * Root importer. I.e. the top parent object.
	 * Make calls on the root to ensure the whole tree is traversed.
	 */
	public function get_root(){
		if(empty($this->parent)){
			return $this;
		}else{
			return $this->parent->get_root();
		}
	}

	/**
	 * Importer's name. By default the class name without the trailing CpImport.
	 */
	public function get_name(){
		$result = get_class($this);
		$result = str_replace('CpImport', '', $result);
		return $result;
	}

	/**
	 * File extentions supported by the importer.
	 * Defaults to importer's name.
	 */
	public function get_extentions(){
		$name = strtolower($this->get_name());
		if(!empty($name)){
			$result = array($name);
		}else{
			$result = array();
		}
		return $result;
	}

	/**
	 * Importer's weight.
	 * Importers with a low value are tested first. Importers with a hight value are tested last.
	 */
	public function get_weight(){
		return 0;
	}

	/**
	 * Returns true if it accepts to import the file passed as parameters.
	 *
	 * @param boolean $settings
	 */
	public function accept($settings){
		$path = $settings->get_path();
		$file_ext = $settings->get_extention();
		$extentions = $this->get_extentions();
		foreach($extentions as $ext){
			if($ext == $file_ext){
				return true;
			}
		}
		return false;
	}

	/**
	 * Import file. Returns the new created object on success or false on failure.
	 * Delegate works to process_import
	 * @param $settings
	 */
	public function import(ObjectImportSettings $settings){
		if($this->accept($settings)){
			if($result = $this->process_import($settings)){
				if($result instanceof ContentObject && $result->has_errors()){
					$errors = $result->get_errors();
					$settings->get_log()->error($errors);

					debug(get_class($this));//@todo: remove that
					debug($errors);//@todo: remove that
					debug($settings);die;//@todo: remove that

					return false;
				}else{//i.e. an array
					return $result;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * Performs the actual importation work. Function should be overriden by sub classes.
	 * @param unknown_type $settings
	 */
	protected function process_import($settings){
		return false;
	}

	/**
	 * Extract a zip file to a temp directory
	 *
	 * @param string $path path to the zip file
	 * @param boolean $delete_file if true zip file is deleted, if false zip file is preserved
	 * @return the temp directory path where the file has been extracted
	 */
	protected function extract($path, $delete_file = true){
		$zip = Filecompression::factory();
		$result = $zip->extract_file($path) .'/';
		if($delete_file){
			Filesystem::remove($path);
		}
		return $result;
	}

	/**
	 * Returns a temp directory under $root.
	 * @param string $root
	 */
	protected function get_temp_directory($root){
		$result = $root . '/d' . Session::get_user_id()  . sha1(time().uniqid()) . '/';
		return $result;
	}

	protected function save(ObjectImportSettings $settings, $object){
		$title = $object->get_title();
		if(empty($title)){
			$object->set_title($this->get_title($settings));
		}
		$description = $object->get_description();
		if(empty($description)){
			$object->set_description($this->get_description($settings));
		}
		$owner_id = $object->get_owner_id();
		if(empty($owner_id)){
			$object->set_owner_id($this->get_owner_id($settings));
		}
		$parent_id = $object->get_parent_id();
		if(empty($parent_id)){
			$object->set_parent_id($this->get_parent_id($settings));
		}
		return $object->save();
	}

	protected function get_title($settings){
		return $settings->get_filename();
	}

	protected function get_description($settings){
		return $settings->get_filename();
	}

	protected function get_owner_id($settings){
		return $settings->get_user()->get_id();
	}

	protected function get_parent_id($settings){
		return $settings->get_category_id();
	}

	/**
	 * If the html file contains a $name meta tag with name equals to $name returns its content attribute.
	 * Otherwise returns $default.
	 *
	 * @param $settings
	 * @param string $name the meta tag name to search for
	 * @returns string the content attribute of the found meta tag or '' if not found.
	 */
	protected function get_meta(ObjectImportSettings $settings, $name, $default = ''){
		if($doc = $settings->get_dom()){
			$name = strtolower($name);
			$list = $doc->getElementsByTagName('meta');
			foreach($list as $meta){
				if(strtolower($meta->getAttribute('name')) == $name){
					return $meta->getAttribute('content');
				}
			}
		}
		return $default;
	}

	/**
	 * Returns the inner html of a node.
	 *
	 * @param $node
	 */
	protected function get_innerhtml($node){
		$result = '';
		$doc = $node->ownerDocument;
		$children = $node->childNodes;
		foreach($children as $child){
			$result .= $doc->saveXml($child);
		}
		return $result;
	}


	/*

	protected function get_object($settings){
	//@todo:see if we reimport existing object or if we create new?

	return $this->object = $this->create_object('');

	}

	protected function create_object($type){
	$result = ContentObject::factory($type);
	$result = empty($result) && class_exists($type) ? new $type() : $result;
	//object must be registered before loading children to avoid circular references
	if($this->get_cache()->has($this->get_path())){
	echo DebugUtil2::print_backtrace_html();
	die;
	}
	$this->get_cache()->register($this->get_path(), $result);
	return $result;
	}

	protected function parse_property($name, $value){
	$names = array(	ContentObject::PROPERTY_CREATION_DATE,
	ContentObject::PROPERTY_MODIFICATION_DATE,
	ComplexContentObjectItem::PROPERTY_ADD_DATE,
	Course::PROPERTY_CREATION_DATE,
	Course::PROPERTY_EXPIRATION_DATE,
	Course::PROPERTY_LAST_EDIT,
	Course::PROPERTY_LAST_VISIT,
	ContentObjectPublication::PROPERTY_PUBLICATION_DATE,
	User::PROPERTY_ACTIVATION_DATE,
	User::PROPERTY_EXPIRATION_DATE,
	User::PROPERTY_REGISTRATION_DATE,
	CalendarEvent::PROPERTY_START_DATE,
	CalendarEvent::PROPERTY_END_DATE );

	foreach($names as $property_name){
	if($property_name == $name){
	$result = ImsXmlReader::parse_date($value);
	return $result;
	}
	}
	return $value;
	}

	protected function read_properties(ImscpObjectReader $item, $filter_in = array()){
	$result = array();
	$children = $item->children();
	foreach($children as $child){
	$name = $child->name();
	if(empty($filter_in) || in_array($name, $filter_in)){
	$value = $child->is_leaf() ? $this->parse_property($name, $child->value()) : $item->get_inner_xml();
	$result[$name] = $value;
	}
	}
	return $result;
	}
	*/
	//Images

	protected function process_images($html){
		$pattern = '~<img.*/>~';
		$result = preg_replace_callback($pattern, array($this, 'process_img'), $html);
		return $result;
	}

	private function process_img($tags){
		foreach($tags as $tag){
			$pattern = '~src="[^"]*"~';
			$matches = array();
			preg_match_all($pattern, $tag, $matches);
			if($src = reset(reset($matches))){
				$src = str_replace('src="', '', $src);
				$src = str_replace('"', '', $src);
				$src = $this->translate_path($src);
				$src = 'src="' . $src . '"';
				$result = preg_replace($pattern, $src, $tag);
				return $result;
			}else{
				return $tag;
			}
		}
	}

	private function translate_path($path){
		$settings = $this->get_settings();
		$file_path = $settings->get_directory() . $path;
		$object_settings = $settings->copy($file_path);
		if($id = CpImport::object_factory($object_settings)->import_content_object()){
			$result = "core.php?go=document_downloader&amp;display=1&amp;object=$id&amp;application=repository";
			return $result;
		}else{
			return $path;
		}
	}

	//delegate to $settings
	/*
	 public function __call($name, $arguments){
		$f = array($this->settings, $name);
		if(is_callable($f)){
		return call_user_func_array($f, $arguments);
		}else{
		return false;
		}
		}*/

}








?>