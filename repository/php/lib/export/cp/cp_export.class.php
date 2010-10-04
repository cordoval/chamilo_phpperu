<?php

require_once dirname(__FILE__) . '/main.php';

/**
 * 
 * Exports both Content Objects and Course objects to the IMS CP format (xml).
 * Each object is exported as a separate file.
 * Each object exporter is responsible to write its entries in the CP manifest file.
 * Supports different object's formats.
 * Call object_factory to get a single object exporter. 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpExport extends ContentObjectExport{
	
	/**
	 * Returns a single object exporter.
	 * 
	 * @param ObjectExportSettings $settings
	 */
    public static function object_factory(ObjectExportSettings $settings){
    	if($export = CpObjectExport::factory($settings)){
    		$result =  new BufferedObjectExport($settings, $export);
    		return $result;
    	}else{
    		return EmptyObjectExport::get_instance();
    	}
    }

    public static function accept($object){
    	return CpObjectExport::accept($object);
    }
    
    
    /**
     * Returns the object's type name.
     * @param DataClass $object
     * @return string
     */
    public static function get_object_type(DataClass $object){
    	$f = array($object, 'get_type');
    	if(is_callable($f)){
    		return $object->get_type();
    	}else{
    		return get_class($object);
    	}
    }
    
    /**
     * Default object name.
     * @param DataClass $object
     */
    public static function get_object_name(DataClass $object){
    	$type = self::get_object_type($object);
    	$id = str_pad($object->get_id(), 8, '0', STR_PAD_LEFT);
    	if($object instanceof ContentObject){
    		return "OBJECT_$id";
    	}else{
	    	return "OBJECT_$type" . "_$id"; //courses and content can have the same id
    	}
    }
    
    /**
     * Default object's file name. 
     * @param unknown_type $object
     */
    public static function get_object_file_name(DataClass $object){
    	return self::get_object_name($object) . '.data.xml';
    }
    
    public function export_content_object(){ 
        $manifest = new ImscpManifestWriter();
        $manifest = $manifest->add_manifest();
        $this->manifest = $manifest;
        $toc = $manifest->add_organizations()->add_organization();
        $directory = $this->get_temp_directory(); 
    	$objects = $this->get_content_object();
    	
    	$settings = new ObjectExportSettings($objects, $directory, $manifest, $toc);
    	$this->export_objects($objects, $settings);
    	$this->export_resource($directory);
    	
        return $this->save($directory, $manifest);
    }
    
    protected function get_temp_directory(){
        $result = Path::get(SYS_TEMP_PATH) . Session::get_user_id() . '/' . uniqid() .'/';
        FileUtil::ensure_directory($result);
        return $result;
    }
    
    protected function export_objects($objects, $settings){
    	$objects = is_array($objects) ? $objects : array($objects);
        foreach ($objects as $object){
        	$this->export_object($object, $settings);
        }
    }
    
    protected function export_object($object, ObjectExportSettings $settings){
    	$object_settings = $settings->copy($object);
    	return self::object_factory($object_settings)->export_content_object();
    }
    
    protected function export_resource($to_directory){
    	$from_directory = dirname(__FILE__).'/resource/'; 
		$files = scandir($from_directory);
		foreach($files as $file){
			if($file !='.' && $file != '..'){
				Filesystem::copy_file($from_directory.$file, $to_directory .'/resources/' . $file);
			} 
		}
    }

    protected function save($directory, $manifest){
    	$manifest->save($directory . ImscpManifestWriter::MANIFEST_NAME);
        
        $zip = Filecompression :: factory();
        $zippath = $zip->create_archive($directory);
        
        Filesystem::remove($directory);
        return $zippath;
    }
	
}






?>