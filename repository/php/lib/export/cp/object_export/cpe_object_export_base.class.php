<?php

require_once (dirname(__FILE__) .'/cp_object_export.class.php');

/**
 * 
 * Base class for CPE object exporters. 
 * Serializes object's data to xml.
 * One object per file.
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpeObjectExportBase extends CpObjectExport{
	
	public function get_type(){
		return ImscpObjectWriter::get_format_full_name();
	} 
	
	public function export_content_object(){
		$object = $this->get_object();
		$settings = $this->get_settings();
		$directory = $settings->get_directory();
		$manifest = $settings->get_manifest();
		$toc = $settings->get_toc();
		    	
		$resource_id = $this->get_resource_id($object);
    	$href = CpExport::get_object_file_name($object);
    	$res = $this->add_resource($manifest, $this->get_type(), $href, $resource_id);
    	$toc = $this->add_toc($object, $toc, $res);
    	
    	$data = $this->serialize();
    	Filesystem::write_to_file($directory.$href, $data);
    	
    	$this->process_files($object);
    	
    	return $directory.$href;
	}
	
	/**
	 * Serialize object. 
	 * @return object xml data 
	 */
    public function serialize(){
    	$writer = new ImscpObjectWriter();
		$object = $this->get_object();
    	//@todo: uncomment that
    	//$writer->add_stylesheet('resources/object_view.xsl');
    	$this->add_object($writer, $object);
    	$this->object_data = $writer->saveXML();
    	$this->object_data = $this->process_images($this->object_data);
    	return $this->object_data;
    }

    /**
     * Add $object to the schema.
     * 
     * @param ImscpObjectWriter $writer
     * @param DataClass $object
     */
    protected function add_object(ImscpObjectWriter $writer, DataClass $object){
    	return false;   
    }
    
    /**
     * Format properties. Ensure that data time properties are correctly encoded as XML data time.
     * @param unknown_type $properties
     */
    protected function format_properties($properties){
    	$result = $properties;
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
    					
    	foreach($names as $name){
    		if(isset($result[$name])){
        		$result[$name] = ImsXmlWriter::format_datetime($result[$name]);
    		}
    	}		
    	
    	$names = array(	User::PROPERTY_PASSWORD,
    					User::PROPERTY_SECURITY_TOKEN);
    					
    	foreach($names as $name){
    		if(isset($result[$name])){
        		unset($result[$name]);
    		}
    	}		
    					
        return $result;
    }
    
    protected function get_object_type(DataClass $object){
    	$f = array($object, 'get_type');
    	if(is_callable($f)){
    		return $object->get_type();
    	}else{
    		return get_class($object);
    	}
    }
    
    protected function get_local_file_path($writer, DataClass $object){
    	if(! is_callable(array($object, 'get_filename'))){
    		return '';
    	}
	    $file_name = $object->get_filename();
    	if(empty($file_name)){
    		return '';
    	}
    	$safe_name = str_safe($file_name);
    	$result = "resources/$safe_name";
    	return $result;
    }
    
    protected function add_identifiers($writer, DataClass $object){
        $identifers = $writer->add_identifiers();
        $object_identifers = chamilo::retrieve_identifiers($object);
        foreach($object_identifers as $catalog=>$name){
        	$identifers->add_identifier($catalog, $name);
        }
    }
    
    protected function add_default_properties(ImsXmlWriter $writer, DataClass $object){
        $properties = $object->get_default_properties();
        if($object instanceof Course){
        	$properties[ContentObject::PROPERTY_CREATION_DATE] = $object->get_creation_date();
        	$properties[ContentObject::PROPERTY_MODIFICATION_DATE] = $object->get_last_edit();
        	$properties[ContentObject::PROPERTY_TYPE] = $this->get_object_type($object);
        }
        
        $properties = $this->format_properties($properties);
        $general = $writer->add_general();
        foreach($properties as $name => $value){
        	$node = $general->add($name)->add_xhml($value);
        }
        
    }
    
    protected function add_additional_properties($writer, DataClass $object){
    	if(!is_callable(array($object, 'get_additional_properties'))){
    		return;
    	}
    	
        $path = $this->get_local_file_path($writer, $object);
    	
        $result = $writer->add_extended();  
        $properties = $object->get_additional_properties();
        $properties = $this->format_properties($properties);
        if(!empty($path)){
        	$properties[Document::PROPERTY_PATH] = $path;
        }
        $result->add($properties);
        return $result;
    }
    
    protected function add_categories($writer, DataClass $object){
    	if(! is_callable(array($object, 'get_parent_id'))){
    		return;
    	}
    	$category_id = $object->get_parent_id();
        if(!empty($category_id)){
        	$categories = $writer->add_categories();
        	$this->add_category($categories, $category_id);
        }
    }
    
    protected function add_category($writer, $category_id){
    	if(empty($category_id)){
    		return;
    	}
    	$category = chamilo::retrieve_category($category_id);
    	$writer = $writer->add_category($category->get_id(), $category->get_name());
		$this->add_category($writer, $category->get_parent());
    }

    protected function process_files($object){
    	if(!is_callable(array($object, 'get_full_path'))){
    		return false;
    	}
    	$path = $object->get_full_path();
    	if(!empty($path) && ($object instanceof Hotpotatoes || $object instanceof LearningPath)){
    		$destination = $this->get_settings()->get_directory().'resources/';
            Filesystem::recurse_copy(dirname($path), $destination, true);
    	}
    }
    
}


















?>