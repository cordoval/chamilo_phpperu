<?php

require_once dirname(__FILE__) . '/cpe_object_export_base.class.php';
require_once_all(dirname(__FILE__) .'/export/*.class.php');

/**
 * Base class for object exporters. 
 * Supports both Content Objects and Courses. 
 * Write object's properties to an xml file. 
 * Expect one object per file.
 * Add xsl procesing instruction to the xml file to provide a default end user view.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpObjectExport{
	
	public static function factory(ObjectExportSettings $settings){
		$directory = dirname(__FILE__).'/export';
		$files = scandir($directory);
		$files = array_diff($files, array('.', '..'));
		foreach($files as $file){
			$class = str_replace('.class.php', '', $file);
			$class = Utilities::underscores_to_camelcase($class);
			include_once("$directory/$file");
			if($result = call_user_func(array($class, 'factory'), $settings)){
				return $result;
			}
		}
		return NULL;
	}
	
	public static function accept($object){
		$directory = dirname(__FILE__).'/export';
		$files = scandir($directory);
		$files = array_diff($files, array('.', '..'));
		foreach($files as $file){
			$class = str_replace('.class.php', '', $file);
			$class = Utilities::underscores_to_camelcase($class);
			include_once("$directory/$file");
			if($result = call_user_func(array($class, 'accept'), $object)){
				return $result;
			}
		}
		return false;
	}
	
	private $settings = null;
	
	public function __construct($settings){
		$this->settings = $settings;
	}
	
	/**
	 * @return ObjectExportSettings
	 */
	public function get_settings(){
		return $this->settings;
	}
	
	/**
	 * Object to serialize
	 * @return DataClass
	 */
	public function get_object(){
		$result = $this->get_settings()->get_object();
		
		if($result instanceof ContentObject){
	   		if(get_class($result) == 'ContentObject'){
    			$result = RepositoryDataManager::get_instance()->retrieve_content_object($result->get_id(), $result->get_type());
    		}
        
        	if(! $result->is_latest_version()){
        		$result = $result->get_latest_version();
        	}
		}
    	if($result instanceof LearningPathItem  || $result instanceof PortfolioItem){
    		$result = Chamilo::retrieve_content_object($result->get_reference());
    	}
		return $result;
	}

	public function get_type(){
		return 'webcontent';
	}  

	public function export_content_object(){
		return false;
	}
	
	protected function export_child($child){
		if(!is_object($child)){
	    	$child = RepositoryDataManager::get_instance()->retrieve_content_object($child);
		}
		if($child instanceof ContentObject){
	   		if(get_class($child) == 'ContentObject'){
    			$child = RepositoryDataManager::get_instance()->retrieve_content_object($child->get_id(), $child->get_type());
    		}
        
        	if(! $child->is_latest_version()){
        		$child = $child->get_latest_version();
        	}
		}
    	if($child instanceof LearningPathItem  || $child instanceof PortfolioItem){
    		$child = Chamilo::retrieve_content_object($child->get_reference());
    	}
    	
		$child_settings = $this->get_settings()->copy($child);
	    $path = CpExport::object_factory($child_settings)->export_content_object();
	    $directory = $this->get_settings()->get_directory();
	    $href = trim(str_replace($directory, '', $path), '/');
	    return $href;
	}
    
	/*
    public function get_metadata($object){ 
	    $lom_mapper = new IeeeLomMapper($object);
	    $lom = $lom_mapper->get_metadata();
	    return $lom->get_dom()->saveXML();
    }*/
    
    //MANIFEST
    
    protected function get_title($object){
    	if($object instanceof LearningPathItem  || $object instanceof PortfolioItem){
    		$object = Chamilo::retrieve_content_object($object->get_reference());
    	}
    	if(is_callable(array($object, 'get_title'))){
    		return $object->get_title();
    	}
    	if(is_callable(array($object, 'get_name'))){
    		return $object->get_name();
    	}
    	debug($object);
    	return '';
    }
    
	protected function add_object_metadata(ImsXmlWriter $item, $object){
		$result = $item->add_metadata('lom', '1.0');
		$lom = new LomWriter($result, 'lom');
		$general = $lom->add_general();
		$general->add_title($this->get_title($object));
		$identifiers = Chamilo::retrieve_identifiers($object);
		foreach($identifiers as $catalog => $id){
			$general->add_identifier($catalog, $id);
		}
		$lifecycle = $lom->add_lifecycle();
		$lifecycle->add_status();
		return $result;
	}
	
	protected function add_toc($object, $toc, $res){
    	$result = $toc->add_item($res);
    	$this->add_object_metadata($result, $object);
    	$result->add_title($this->get_title($object));
    	return $result;
	}
	
	protected function add_resource(ImscpManifestWriter $manifest, $mime_type, $href, $resource_id){
    	$result = $manifest->get_resources()->add_resource($mime_type, $href, $resource_id);
    	$result->add_file($href);
    	return $result;
	}
	
	protected function add_manifest_entry($object, $href){
		$settings = $this->get_settings();
    	$resource_id = $this->get_resource_id($object);
    	$res = $this->add_resource($settings->get_manifest(), $this->get_type(), $href, $resource_id);
    	$toc = $this->add_toc($object, $settings->get_toc(), $res);
    	return $toc;
	}
	
	//process image

    protected function process_images($html){
    	$pattern = '~<img.*/>~';
    	$result = preg_replace_callback($pattern, array($this, 'process_img'), $html);
    	return $result;
    }
    
    private function process_img($tags){
    	foreach($tags as $tag){
	    	$pattern = '~src="\S*"~';
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
    	$head = 'core.php?';
    	if($is_object_url = (str_left($path, strlen($head)) == $head)){
	    	$path = str_replace($head, '', $path);
	    	$args = explode('&amp;', $path);
	    	foreach($args as $arg){
	    		$parts = explode('=', $arg);
	    		if($parts[0] == 'object'){
	    			$id = $parts[1];
	    			return $this->export_child($id);
	    		}
	    	}
    	}else if($is_local_url = (str_left($path, 4) !='http')){
    		//@todo:
    		debug('not implemented');
    		$filename = basename($path);
    		$href = "resources/$filename";
    		$directory = $this->get_settings()->get_directory();
    		if(Filesystem::copy_file($path, $directory.$href)){
    			return $href;
    		}else{
    			$log = $this->get_settings()->get_log();
    			$log->error($path);
    			return $path;
    		}
    	}
    	return $path;
    }
	
    //UTIL
    
    protected function get_resource_id($object){
    	$type = CpExport::get_object_type($object);
    	$id = str_pad($object->get_id(), 8, '0', STR_PAD_LEFT);
    	if($object instanceof ContentObject){
    		return "RESOURCE_$id";
    	}else{
	    	return "RESOURCE_$type" . "_$id"; //courses and content can have the same id
    	}
    }
    
    protected function get_main_css(){
    	$result = '<style type="text/css">';
    	$result .= file_get_contents(dirname(__FILE__).'/../resource/main.css');
    	$result .= '';
    	$result .= '</style>';
    	return $result;
    }

    protected function get_file_name($object, $ext = ''){
    	$id = $object->get_id();
    	$id = str_pad($id, 8, '0', STR_PAD_LEFT);
    	$title = $object->get_title();
    	$ext = $ext ? '.' . $ext : '';
    	$result = str_safe($title . '_' . $id . $ext);
    	return $result;
    }
    
    function _add_files($temp_dir)
    {
        foreach ($this->files as $hash => $path)
        {
            $newfile = $temp_dir . 'data/' . $hash;
            Filesystem :: copy_file($path, $newfile, true);
        }
        foreach ($this->hotpot_files as $hotpot_dir)
        {
            $newfile = $temp_dir . 'hotpotatoes/' . basename(rtrim($hotpot_dir, '/'));
            Filesystem :: recurse_copy($hotpot_dir, $newfile, true);
        }
        
        foreach ($this->scorm_files as $scorm_dir)
        {
            $newfile = $temp_dir . 'scorm/' . basename(rtrim($scorm_dir, '/'));
            Filesystem :: recurse_copy($scorm_dir, $newfile, true);
        }
    }
    
    function _export_additional_properties($co)
    {
        if ($co->get_type() == 'document')
        {
            $this->files[$co->get_hash()] = $co->get_full_path();
        }
        
        if ($co->get_type() == 'hotpotatoes')
        {
            $this->hotpot_files[] = dirname($co->get_full_path());
        }
        
        if ($co->get_type() == 'learning_path' && $co->get_path())
        {
            $this->scorm_files[] = $co->get_full_path();
        }
        
        if ($co->get_type() == 'learning_path_item' || $co->get_type() == 'portfolio_item')
        {
            $id = $co->get_reference();
            $this->render_content_object(chamilo::retrieve_content_object($id));
            $co->set_reference('object' . $id);
        }
        
        if($co->get_type() == 'hotspot_question')
        {
        	$co->set_image('object' . $co->get_image());
        }
    }

}


















?>