<?php

/**
 * Export Content Objects. 
 * Write object's properties to an xml file
 * Children, attachments, includes are exported as separate files.
 * Only the links to the children's files are stored. 
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpContentObjectExport extends CpeObjectExportBase{
	
	public static function factory($settings){
		$object = $settings->get_object();
		if($object instanceof ContentObject){
			return new self($settings);
		}else{
			return null;
		}
	} 

	public function get_type(){
		return ImscpObjectWriter::get_format_full_name() . '#ContentObject';
	} 
	
    protected function add_object(ImscpObjectWriter $writer, DataClass $object){   
    	$catalog = chamilo::get_local_catalogue_name();
    	$id = $object->get_id();
    	$type = $this->get_object_type($object);
        $writer = $writer->get_objects()->add_object($catalog, $id, $type);
        
        $this->add_identifiers($writer, $object);
        $this->add_default_properties($writer, $object);
        $this->add_additional_properties($writer, $object);
        $this->add_categories($writer, $object);
        $this->add_children($writer, $object);
        $this->add_attachments($writer, $object);
        $this->add_includes($writer, $object);
    }
    
    protected function add_children($writer, ContentObject $object){
    	if(! is_callable(array($object, 'is_complex_content_object'))){
    		return;
    	}
    	if(! $object->is_complex_content_object()){
    		return;
    	}
    	
        $children = chamilo::retrieve_children($object);
        $sub_items = ($children->size() > 0) ? $writer->add_subItems() : null;
        while($child = $children->next_result()){
	        $properties = $child->get_default_properties();
    		$child_object = Chamilo::retrieve_content_object($child->get_ref());
	        if($child_object instanceof LearningPathItem  || $child_object instanceof PortfolioItem){
	    		$child_object = Chamilo::retrieve_content_object($child_object->get_reference());
	    	}
    		$properties[ContentObject::PROPERTY_TITLE] = $child_object->get_title();
    		$properties[ContentObject::PROPERTY_DESCRIPTION] = $child_object->get_description();
        	$properties = $this->format_properties($properties);
            $ref = $child_object->get_id();
            $id = $child->get_id();
	        $href = $this->export_child($child_object);
            $sub_item = $sub_items->add_subItem($href, $ref, $id);
            $general = $sub_item->add_general();
	        foreach($properties as $name => $value){
	        	$general->add($name)->add_xml($value);
	        }
	        $general->add(ContentObject::PROPERTY_TYPE)->add_xml(get_class($child));
	        $extended = $sub_item->add_extended();
	        $properties = $child->get_additional_properties();
	        $properties = $this->format_properties($properties);
	        foreach($properties as $name => $value){
	        	$extended->add($name)->add_xml($value);
	        }
	        
        }
    }

    protected function add_attachments($writer, ContentObject $object){
    	if(! is_callable(array($object, 'get_attached_content_objects'))){
    		return;
    	}
        $attachments = $object->get_attached_content_objects();
        $writer = (count($attachments) > 0) ? $writer->add_attachments() : $writer;
        foreach($attachments as $attachment){
        	$id = $attachment->get_id();
	    	$href = $this->export_child($attachment);
        	$writer->add_attachment($href, $id);
        }
    }
    
    protected function add_includes($writer, ContentObject $object){
    	if(! is_callable(array($object, 'get_included_content_objects'))){
    		return;
    	}
        $includes = $object->get_included_content_objects();
        $writer = (count($includes) > 0) ? $writer->add_includes() : $writer;
        foreach($includes as $include){
        	$id = $include->get_id();
        	//force reloads by passing id instead of object
        	//@todo: get_included_content_objects do not return proper object type - i.e. document
	    	$href = $this->export_child($id); 
        	$writer->add_includes($href, $id);
        }
    }


}


















?>