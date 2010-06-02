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
	
    static function factory_qti($content_object, $manifest, $directory){
    	if($result = SerializerExport::factory($content_object, $manifest, $directory)){
    		return $result;
    	}else{
    		return null;
    	}
    }
    
    private $manifest = null;
    private $directory = '';
    
    function __construct($content_object, $manifest=null, $directory=''){
        parent :: __construct($content_object);
        $manifest = empty($manifest) ? new ImsCpmanifestWriter() : $manifest;
        $this->manifest = $manifest;
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
	
    public function export_content_object(){
    	$items = $this->get_content_object();
    	$items = is_array($items) ? $items : array($items);
    	foreach($items as $item){ 
        	if($exporter = self::factory_qti($item, $this->get_manifest(), $this->get_temp_directory())){
        		$result = $exporter->export_content_object();
        	}else{
        		
        	}
    	}
    	
   		$xml = $this->get_manifest()->saveXML();
   		$file_name = ImsCpmanifestWriter::MANIFEST_NAME;
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
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $file_path = $this->get_temp_directory() . $file_name;
        $doc->save($file_path);
        return $file_path;
    }
	
    protected function add_manifest_resource($object){
    	$manifest_resources =  $this->get_manifest()->get_resources();
		$type = 'imsqti_item_xmlv2p1';
		$href = SerializerBase::file_name($object);
		$id = str_replace('.xml', '', $href);
		$result = $manifest_resources->add_resource($type, $href, $id);
		$this->add_object_metadata($result, $object);
		$result->add_file($href);
		return $result;
    }

	protected function add_object_metadata(ImsXmlWriter $item, $object){
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
    
    
    
    
}