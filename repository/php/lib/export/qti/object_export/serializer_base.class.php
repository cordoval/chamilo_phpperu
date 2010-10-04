<?php

/**
 * Base class for serializers.
 * Includes resources management and path translation.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SerializerBase{
	
	/**
	 * @return SerializerBase
	 */
	static function factory($question, $target_root, $directory, $manifest, $toc){
		$dir = dirname(__FILE__) . '/serializer/';
		$files = scandir($dir);
		foreach($files as $file){
			if($file != '.' && $file !='..'){
				$path = $dir . $file;
				require_once $path;
				$type = str_replace('.class.php', '', $file);
        		$class = Utilities :: underscores_to_camelcase($type);
        		$f = array($class, 'factory');
        		if($result = call_user_func($f, $question, $target_root, $directory, $manifest, $toc)){
        			return $result;
        		}
			}
		}
		return null;
	}
	
	public static function file_name(ContentObject $object){
		$id = $object->get_id();
		$type = $object->get_type();
		$type = strpos($type, 'question') ? 'question': $type;
		$result = $type .'_qti_' . str_pad($id, 8, '0', STR_PAD_LEFT).'.xml';
		return $result;
	}
	
	static function get_identifier($object){
		$id = $object->get_id();
		$id = str_pad($id, 8, '0', STR_PAD_LEFT);
		$server_name = $_SERVER['SERVER_NAME'];
		$result = "chamilo:$server_name:ID_$id";
		return $result;
	}
		
	private $resources = array();
	private $manifest = null;
	private $toc = null;
	private $directory = '';
	
	public function __construct($item, $directory='', $manifest, $toc){
		if(is_string($item)){
			$this->resource_manager = new QtiExportResourceManager($item);
		}else{
			$this->resource_manager = $item;
		}
		$this->directory = $directory;
		$this->manifest = $manifest;
		$this->toc = $toc;
	}
	
	public function get_temp_directory(){
		return $this->directory;
	}
	
	public function get_manifest(){
		return $this->manifest;
	}

	public function get_toc(){
		return $this->toc;
	}
	
	public function get_resources(){
		return $this->resources;
	}

	public function register_resource($id, $local_path){
		$this->resources[$id] = $local_path;
	}
	
	public function serialize($object){
		throw new Exception('Not implemented');
	}
	
	//SAVE
	
	/**
	 * Save $object data file and related ressources - attached files, images, etc -
	 * Write entry to manifest but do not save manifest.
	 * 
	 * @param ContentObject $object
	 */
	public function save($object){
   		$resource_id = 'RESOURCE_'. str_pad($object->get_id(), 8, '0', STR_PAD_LEFT);
   		$parent_toc = $this->toc;
    	$this->toc = $this->add_manifest_toc($object, $this->toc, $resource_id);
    	
   		$xml = $this->serialize($object);
   		$file_name = $this->file_name($object);
   		$result = $this->create_qti_file($file_name, $xml);
   		
   		if($result){
   			$resources = $this->get_resources();
	   		foreach($resources as $item => $local_path){
	   			$this->export_resource($item, $local_path);
	   			
	   		}
        	$this->add_manifest_resource($this->get_manifest(), '', $file_name, $resource_id);
   		}
   		$this->toc = $parent_toc;
   		return $result;
    }
    
    protected function create_qti_file($file_name, $xml){
        $file_path = $this->get_temp_directory() . $file_name;
    	Filesystem::write_to_file($file_path, $xml);
        return $file_path;
    }
    
    protected function export_resource($item, $local_path){
	   	$to_path = $this->get_temp_directory() . $local_path;
    	if(is_numeric($item)){
    		$document_id = (int)$item;
	        $document = RepositoryDataManager::get_instance()->retrieve_content_object($document_id);
	        $from_path =  $document->get_full_path();
	    	Filesystem::copy_file($from_path, $to_path, true);
    	}else if(is_object($item)){
    		$document = $item;
	        $from_path =  $document->get_full_path();
	    	Filesystem::copy_file($from_path, $to_path, true);
    	}else{
    		$from_path = $item;
	    	$result = Filesystem::copy_file($from_path, $to_path, true);
    	}
    }
    
    //MANIFEST

	protected function add_manifest_object_metadata(ImsXmlWriter $item, $object){
		$result = $item->add_metadata('lom', '1.0');
		$lom = new LomWriter($result, 'lom');
		$general = $lom->add_general();
		$general->add_title($object->get_title());
		$identifiers = Chamilo::retrieve_identifiers($object);
		foreach($identifiers as $catalog => $id){
			$general->add_identifier($catalog, $id);
		}
		$lifecycle = $lom->add_lifecycle();
		$lifecycle->add_status();
		return $result;
	}
	
	protected function add_manifest_toc($object, $toc, $res){
    	$result = $toc->add_item($res);
    	$this->add_manifest_object_metadata($result, $object);
    	$result->add_title($object->get_title());
    	return $result;
	}
	
	protected function add_manifest_resource($manifest, $mime_type = 'imsqti_item_xmlv2p1', $href, $id){
		$mime_type = empty($mime_type) ? 'imsqti_item_xmlv2p1' : $mime_type;
    	$result = $manifest->get_resources()->add_resource($mime_type, $href, $id);
    	$result->add_file($href);
    	return $result;
	}
		
	//TRANSLATE TEXT
	
	
	protected function translate_text($text, $question=null, $text_format=''){
		if(empty($text)){
			return $text;
		}
		$doc = new DOMDocument();
		$doc->loadHTML('<?xml encoding="UTF-8">' . $text);
    	$this->translate_nodes($doc->childNodes);
    	$body = $doc->getElementsByTagName('body')->item(0);
    	
    	$result = $doc->saveXML($body);
    	$result = str_replace('<body>', '', $result);
    	$result = str_replace('</body>', '', $result);
    	return $result;
	}
	
	
	private function translate_node($node){
		$name = isset($node->nodeName) ? $node->nodeName : '';
    	if($name == 'img'){
    		$this->rewrite_path($node, 'src');
    	}else if($name == 'object'){
    		$this->rewrite_path($node, 'data');
    	}
	
    	$this->translate_nodes($node->childNodes);
	}
	
	
	private function translate_nodes($nodes){
		if(empty($nodes)){
			return;
		}
    	for($i = 0, $length = $nodes->length; $i<$length; $i++){
    		$node = $nodes->item($i);
    		$this->translate_node($node);
    	}
	}

    private function rewrite_path($node, $attribute){
    	if(!$node->hasAttribute($attribute)) return;
    	
    	$path = $node->getAttribute($attribute);
    	$path = $this->translate_path($path);
    	$node->setAttribute($attribute, $path);
    }

    private function translate_path($path){
    	$head = 'core.php?';
    	if($is_object_url = (str_left($path, strlen($head)) == $head)){
	    	$path = str_replace($head, '', $path);
	    	$args = explode('&', $path);
	    	foreach($args as $arg){
	    		$parts = explode('=', $arg);
	    		if($parts[0] == 'object'){
	    			$id = $parts[1];
	    			$image = RepositoryDataManager::get_instance()->retrieve_content_object($id);
	    			$filename = $image->get_filename();
	    			$filename = Filesystem::create_safe_name($filename);
	    			$result = "resources/$filename";
	    			$this->register_resource($id, $result);
	    			return $result;
	    		}
	    	}
    	}else if($is_local_url = (str_left($path, 4) !='http')){
    		$filename = basename($path);
    		$filename = Filesystem::create_safe_name($filename);
    		$result = "resources/$filename";
    		$this->register_resource($path, $result);
    		return $result;
    	}
    	return $path;
    }
    
}





?>