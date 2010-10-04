<?php

/**
 * 
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class CpImportContentObject extends CpObjectImportBase{
	
	public static function factory(ObjectImportSettings $settings){
		$type = strtolower($settings->get_type());
		if($type == 'ceo_v1p0#contentobject'){
			return new self($settings);
		}else if(empty($type) && Ceo::is_ceo_content_object_file($settings->get_path())){
			return new self($settings);
		}else{
			return null;
		}
	}
	
	private $objects = array();
	   
    public function import_content_object(){
    	$reader = new ImscpObjectReader($this->get_path(), false);
    	$item = $reader->get_objects()->first_object();
    	$ids = $this->get_identifiers($item);
    	$type = $item->type;
    	$object = $this->create_object($type);
    	
    	$this->process_general($object, $item);
    	$this->process_extended($object, $item);
    	$this->process_categories($object, $item);
        $object->save();//id is used by other objects
        
    	$this->process_attachments($object, $item);
    	$this->process_includes($object, $item);
    	$this->process_children($object, $item);
    	
        $result = $object->save();
        if(!$result){
        	$log = $this->get_log();
        	$log->error($object->get_errors());
        }
        return $object; //->get_id()
    }
            
    protected function process_general(ContentObject $object, ImscpObjectReader $item){
    	$properties = array(ContentObject::PROPERTY_TITLE ,
    						ContentObject::PROPERTY_DESCRIPTION , 
    						ContentObject::PROPERTY_STATE , 
    						ContentObject::PROPERTY_COMMENT ,
    						);
    	$children = $item->get_general()->children();
    	foreach($children as $child){
    		$name = $child->name();
    		$value = $child->value();
    		if(in_array($name, $properties)){
    			$object->set_default_property($name, $value);
    		}
    	}
    	$description = $item->get_general()->get_description()->get_inner_xml();
    	$description = $this->process_images($description);
    	$object->set_description($description);
    	$object->set_parent_id($this->get_settings()->get_category());
    	$object->set_state(ContentObject::STATE_NORMAL);
        $object->set_owner_id($this->get_settings()->get_user()->get_id());
    }
    
    protected function process_extended(ContentObject $object, ImscpObjectReader $item){
    	$names = $object->get_additional_property_names();
    	$children = $item->get_extended()->children();
    	foreach($children as $child){
    		$name = $child->name();
    		if(in_array($name, $names)){
    			$value = $this->parse_property($name, $child->value());
    			$object->set_additional_property($name, $value);
    		}
    	}
    }
    
    protected function process_categories(ContentObject $object, ImscpObjectReader $item){
    	$categories = array();
    	$item = $item->get_categories()->get_category();
    	while($name = $item->get_name()->value()){
    		$categories[] = $name;
    		$item = $item->get_category(); 
    	}
    	$categories = array_reverse($categories);
    	$category_id = $this->get_settings()->get_category();
    	foreach($categories as $category){
        	$category_id = $this->get_category($category, $category_id)->get_id();
    	}
    	$object->set_parent_id($category_id);
    }
    
    protected function get_category($name, $parent_id){
        if($result = Chamilo::get_category_by_name($name, $parent_id)){
        	return $result;
        }else{
        	return $this->create_category($name, $parent_id);
        }
    }
    
    protected function create_category($name, $parent_id){
    	$category = new RepositoryCategory();
    	$category->set_name($name);
        $category->set_parent($parent_id);
        $category->set_user_id($this->get_user()->get_id());
        $category->save();
        return $category;
    }
       
    protected function process_attachments(ContentObject $object, ImscpObjectReader $item){
    	$settings = $this->get_settings();
    	$items = $item->get_attachments()->list_attachment();
    	foreach($items as $item){
    		$path = $settings->get_directory() . $item->href;
    		$co_settings = $settings->copy($path, '');
    		if($child = CpImport::object_factory($co_settings)->import_content_object()){
    			$object->attach_content_object($child->get_id());
    		}
    	}
    }
    
    protected function process_includes(ContentObject $object, ImscpObjectReader $item){
    	$settings = $this->get_settings();
    	$items = $item->get_includes()->list_include();
    	foreach($items as $item){
    		$path = $settings->get_directory() . $item->href;
    		$co_settings = $settings->copy($path, '');
    		if($child = CpImport::object_factory($co_settings)->import_content_object()){
    			$object->include_content_object($child->get_id());
    		}
    	}
    }
    
    protected function process_children(ContentObject $object, ImscpObjectReader $item){
    	$store = ContentObject::get_data_manager();
    	$settings = $this->get_settings();
    	$items = $item->get_subItems()->list_subItem();
    	foreach($items as $item){
    		$path = $settings->get_directory() . $item->href;
    		$co_settings = $settings->copy($path, '');
    		if($child = CpImport::object_factory($co_settings)->import_content_object()){
                if($object instanceof Portfolio && !($child instanceof Portfolio)){
                	$child = new PortfolioItem();
                	$child->set_reference($child->get_id());
                	$child->save();
                }
                if($object instanceof LearningPath){
                	$cloi = new ComplexLearningPathItem();
                }else if($object instanceof Portfolio){
                	$cloi = new ComplexPortfolioItem();
                }else{
	                $cloi = ComplexContentObjectItem::factory($child->get_type());
                }
                $cloi->set_ref($child->get_id());
                $cloi->set_user_id($settings->get_user()->get_id());
                $cloi->set_parent($object->get_id());
                $cloi->set_display_order($store->select_next_display_order($object->get_id()));
                $properties = $item->get_extended()->children();
                foreach($properties as $property){
                	$name = $property->name();
                	$value = $property->value();
                	$cloi->set_additional_property($name, $value);
                }
                $cloi->save(); 
                }
    		
    	}
    }
    
    
    
    /*
    
    protected function set_system_id($file_id, $system_id){
    	$this->objects[$file_id] = $system_id;
    	return $system_id;
    }
    
    protected function has_system_id($file_id){
    	return isset($this->objects[$file_id]);
    }
    
    protected function get_system_id($file_id){
    	if(isset($this->objects[$file_id])){
    		return $this->objects[$file_id];
    	}else{
    		return 0;
    	}
    }
    
    public function get_identifiers($item){
    	$result = array();
    	$ids = $item->get_identifiers()->list_identifier();
    	foreach($ids as $id){
    		$catalog = $id->catalog;
    		$entry = $id->entry;
    		$result[$catalog] = $entry;
    	}
    	return $result;
    }
    */
}






?>