<?php

/**
 * Adapter between serializer and exporter.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SerializerExport extends QtiExport{
	
	public static function factory($object, $directory, $manifest, $toc){
    	if($serializer = SerializerBase::factory($object, '', $directory, $manifest, $toc)){
	        return new self($object, $serializer, $directory, $manifest, $toc);
    	}else{
    		return null;
    	}
	}
	
	private $serializer = null;
	
	public function __construct($object, $serializer, $directory, $manifest, $toc){
		parent::__construct($object, $directory, $manifest, $toc);
		$this->serializer = $serializer;
	}
	
	public function get_serializer(){
		return $this->serializer;
	}

    public function export_content_object(){
    	$object = $this->get_content_object();
    	$directory = $this->get_temp_directory();
    	$manifest = $this->get_manifest();
    	$toc = $this->get_toc();
   		$serializer = $this->get_serializer();
   		return $serializer->save($object, $directory, $manifest, $toc);
    }
   
}






?>