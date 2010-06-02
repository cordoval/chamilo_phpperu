<?php

/**
 * Adapter between serializer and exporter.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SerializerExport extends QtiExport{
	
	public static function factory($object, $manifest, $directory){
    	if($serializer = SerializerBase::factory($object, '', $manifest, $directory)){
	        return new self($object, $serializer, $manifest, $directory);
    	}else{
    		return null;
    	}
	}
	
	private $serializer = null;
	
	public function __construct($object, $serializer, $manifest, $directory){
		parent::__construct($object, $manifest, $directory);
		$this->serializer = $serializer;
	}
	
	public function get_serializer(){
		return $this->serializer;
	}

    public function export_content_object(){
    	$object = $this->get_content_object();
   		$serializer = $this->get_serializer();
   		$xml = $serializer->serialize($object);
   		$file_name = $serializer->file_name($object);
   		$result = $this->create_qti_file($file_name, $xml);
   		
   		if($result){
   			$resources = $serializer->get_resources();
	   		foreach($resources as $item => $local_path){
	   			$this->export_resource($item, $local_path);
	   		}
        	$this->add_manifest_resource($object);
   		}
   		return $result;
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

}