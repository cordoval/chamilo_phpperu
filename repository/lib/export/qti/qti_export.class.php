<?php
/**
 * $Id: qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti
 */
require_once dirname(__FILE__) . '/main.php';

/**
 * Exports learning object to QTI format (xml)
 */
class QtiExport extends ContentObjectExport{
	
    static function factory_qti($content_object, $directory, $manifest, $toc){
    	if($result = SerializerExport::factory($content_object, $directory, $manifest, $toc)){
    		return $result;
    	}else{
    		return null;
    	}
    }
    
    private $manifest = null;
    private $directory = '';
    private $toc = null;
    
    function __construct($content_object, $directory='', $manifest=null, $toc=null){
        parent :: __construct($content_object);
        if(empty($manifest)){
	        $manifest = new ImscpManifestWriter();
	        $manifest = $manifest->add_manifest();
	        $this->manifest = $manifest;
	        $this->toc = $manifest->add_organizations()->add_organization();
        }else{
	        $this->manifest = $manifest;
	        $this->toc = $toc;
        }
        
        if(empty($directory)){
	    	$directory = Path :: get(SYS_TEMP_PATH) .Session::get_user_id(). '/export_qti/';
	        if (! is_dir($directory)){
	            mkdir($directory, '0777', true);
	        }
        }
	    $this->directory = $directory;
    }
    
    public function get_manifest(){
		return $this->manifest;    	
    }
	
    public function get_toc(){
    	return $this->toc;
    }
    
    public function export_content_object(){
    	$items = $this->get_content_object();
    	$items = is_array($items) ? $items : array($items);
    	foreach($items as $item){ 
    		$directory = $this->get_temp_directory();
    		$manifest = $this->get_manifest();
    		$toc = $this->toc;
        	if($exporter = self::factory_qti($item, $directory, $manifest, $toc)){
        		$result = $exporter->export_content_object();
        	}else{
        		
        	}
    	}
    	
   		$xml = $this->get_manifest()->saveXML();
   		$file_name = ImscpManifestWriter::MANIFEST_NAME;
   		$this->create_qti_file($file_name, $xml);
    	 
    	$temp_dir = $this->get_temp_directory();
        $zip = Filecompression :: factory();
        $zip->set_filename('qti', 'zip');
        $zippath = $zip->create_archive($temp_dir);
        Filesystem :: remove($temp_dir);
        return $zippath;
    }
    
    protected function get_temp_directory(){
        return $this->directory;
    }
    
    protected function create_qti_file($file_name, $xml){
        $file_path = $this->get_temp_directory() . $file_name;
    	Filesystem::write_to_file($file_path, $xml);
        return $file_path;
    }
	   
}






?>