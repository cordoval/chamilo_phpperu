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
class CpImportCourse extends CpObjectImportBase{
	
	public static function factory(ObjectImportSettings $settings){
		$type = strtolower($settings->get_type());
		if($type == 'ceo_v1p0#course'){
			return new self($settings);
		}else if(empty($type) && Ceo::is_ceo_course_file($settings->get_path())){
			return new self($settings);
		}else{
			return null;
		}
	}
	
    public function import_content_object(){
    	$reader = new ImscpObjectReader($this->get_path(), false);
    	$item = $reader->get_objects()->first_object();
    	$ids = $this->get_identifiers($item);
    	$type = $item->type;
    
    	$object = $this->create_object($type);
    	$this->process_general($object, $item);
    	$this->process_categories($object, $item);
        $result = $object->create(); //needed to get the object id for publications
        
    	$relation = new CourseUserRelation();
    	$relation->set_course($object->get_id());
    	$relation->set_user($this->get_settings()->get_user()->get_id());
    	$relation->set_role(0);
    	$relation->set_status(1);
    	$relation->set_category(0);
    	$relation->save();
    	
    	$this->process_settings($object, $item);
    	$this->process_layout_settings($object, $item);
    	$this->process_rights($object, $item);
    	$this->process_type($object, $item);
    	$this->process_introduction($object, $item);
    	$this->process_sections($object, $item);
    	$this->process_publications($object, $item);
    	$this->process_groups($object, $item);
    	$this->process_user_relations($object, $item);
        $result = $object->save();
        if(!$result){
        	$log = $this->get_log();
        	$log->error($object->get_errors());
        }
        return $object; //->get_id()
    }
        
    protected function create_category($name){
    	$result = new CourseCategory();
    	$result->set_name($name);
    	$result->create();
    	return $result;
    }

    protected function process_general(Course $object, ImscpObjectReader $item){
    	$properties = array(Course::PROPERTY_VISUAL ,
    						Course::PROPERTY_NAME , 
    						Course::PROPERTY_EXTLINK_NAME , 
    						Course::PROPERTY_EXTLINK_URL , 
    						Course::PROPERTY_EXPIRATION_DATE ,
    						);
    	
    	$children = $item->get_general()->children();
    	foreach($children as $child){
    		$name = $child->name();
    		$value = $child->value();
    		if(in_array($name, $properties)){
    			$object->set_default_property($name, $value);
    		}
    	}
    	$object->set_titular($this->get_settings()->get_user()->get_id());
    	$object->set_course_type_id(0);
    	$code = $object->get_visual();
    	if(! Course::get_data_manager()->is_visual_code_available($code)){
    		$object->set_visual($code .'-'. time());
    	}
    }
    
    protected function process_settings(Course $object, ImscpObjectReader $item){
    	$settings = $object->get_settings();
    	$children = $item->get_settings()->get_general()->children();
    	foreach($children as $child){
    		$name = $child->name();
    		$value = $child->value();
    		if( $name != CourseSettings::PROPERTY_COURSE_ID &&
    			$name != CourseSettings::PROPERTY_ID){
    				$settings->set_default_property($name, $value);
    			}
    	}
    	$general = $item->get_settings()->get_general();
    	$max = $general->get_max_number_of_members()->value();
    	$max = empty($max) ? 0 : $max;
    	$object->set_max_number_of_members($max);
    	$object->set_language(strtolower($general->get_language()->value()));
    	$visibility = $general->get_visibility()->value();
    	$visibility = empty($visibility) ? false : true;
		$object->set_visibility($visibility);
    	$access = $general->get_access()->value();
    	$access = empty($access) ? false : true;
		$object->set_access($access);
    	$settings->save();
    }
    
    protected function process_layout_settings(Course $object, ImscpObjectReader $item){
    	$settings = $object->get_layout_settings();
    	$children = $item->get_layout_settings()->get_general()->children();
    	foreach($children as $child){
    		$name = $child->name();
    		$value = $child->value();
    		if( $name != CourseLayout::PROPERTY_COURSE_ID &&
    			$name != CourseLayout::PROPERTY_ID){
    				$settings->set_default_property($name, $value);
    			}
    	}
    }
    
    protected function process_rights(Course $object, ImscpObjectReader $item){
    	$rights = $object->get_rights();
    	$children = $item->get_rights()->get_general()->children();
    	foreach($children as $child){
    		$name = $child->name();
    		$value = $child->value();
    		if( $name != CourseRights::PROPERTY_COURSE_ID &&
    			$name != CourseRights::PROPERTY_ID){
    				$rights->set_default_property($name, $value);
    			}
    	}
    }
    
    protected function process_type(Course $object, ImscpObjectReader $item){
    	$item = $item->first_type();
        $condition = new EqualityCondition(CourseType::PROPERTY_NAME, $item->name);
        if($type = CourseType::get_data_manager()->retrieve_object(CourseType::get_table_name(), $condition)){
        	$object->set_course_type_id($type->get_id());
        }else{
        	//@todo:create a new type?
        }
    }
    
    protected function process_introduction(Course $object, ImscpObjectReader $item){
    	//not needed
    	
    	/*
    	$settings = $this->get_settings();
    	$object->set_intro_text(true);
    	$value = $item->get_introduction()->value();
    	$text = $item->get_introduction()->text();
    	$intro = new Introduction();
    	$intro->set_title(substr($text, 0, 150));
    	$intro->set_description($value);
    	$intro->save();
    	
    	$pub = new ContentObjectPublication();
    	$pub->set_course_id($object->get_id());
    	$pub->set_content_object_id($intro->get_id());
    	$pub->set_hidden(false);
    	$pub->set_publisher_id($settings->get_user()->get_id());
    	$pub->set_parent_id(0);
    	$pub->set_category_id(0);
    	$pub->set_from_date(0);
    	$pub->set_to_date(0);
    	$time = time();
    	$pub->set_publication_date($time);
    	$pub->set_modified_date($time);
    	$pub->save();*/
    }
    
    protected function process_sections(Course $object, ImscpObjectReader $item){
    	$standard_sections = array('Tools', 'Links', 'Disabled', 'Course administration');
    	$items = $item->get_sections()->list_section();
    	foreach($items as $item){
    		$name = $item->get_general()->get_name()->value();
    		if(! in_array($name, $standard_sections)){
    			$section = new CourseSection();
    			$section->set_course_code($object->get_id());
    			$section->set_name($name);
    			$section->set_visible(true);
    			$type = $item->get_general()->get_type()->value();
    			$type = empty($type) ? 1 : $type;
    			$section->set_type($type);
    			$section->save();
    			$module_items = $item->get_modules()->list_module();
    			$tool_list = $object->get_tools();
    			$tools = array();
    			foreach($tool_list as $tool){
    				$tools[strtolower($tool->get_name())] = $tool;
    			}
    			foreach($module_items as $module_item){
    				$module = $tools[strtolower($module_item->name)];
    				if(empty($module)){	
    					$module = new CourseModule();
    					$module->set_name($module_item->name);
    					$module->set_course_code($object->get_id());
    					$module->set_visible(true);
    				}
    				$module->set_section($section->get_id());
    				$module->save();
    			}
    		}
    	}
    }
    
    protected function process_publications(Course $object, ImscpObjectReader $item){
    	$settings = $this->get_settings();
    	$items = $item->get_publications()->list_publication();
    	foreach($items as $item){
    		$path = $settings->get_directory() . $item->href;
    		$co_settings = $settings->copy($path, '');
    		if($content_object = CpImport::object_factory($co_settings)->import_content_object()){
		    	$pub = new ContentObjectPublication();
		    	$pub->set_course_id($object->get_id());
		    	$pub->set_content_object_id($content_object->get_id());
		    	$pub->set_tool($item->get_general()->get_tool()->value());
		    	$pub->set_hidden(false);
		    	$pub->set_publisher_id($settings->get_user()->get_id());
		    	$pub->set_parent_id(0);
		    	$pub->set_category_id(0);
		    	$pub->set_from_date(0);
		    	$pub->set_to_date(0);
    			$time = time();
		    	$pub->set_publication_date($time);
		    	$pub->set_modified_date($time);
		    	$pub->save();
    		}else{
    			//debug($item->get_current());
    			//debug($co_settings);
    			//die;
    		}
    	}
    }
    
    protected function process_groups(Course $object, ImscpObjectReader $item, $parent = null){
    	$items = $item->get_groups()->list_group();
    	foreach($items as $item){
    		$general = $item->get_general();
    		$name = $general->get_name()->value();
    		//root group is created automatically. we don't recreate it.
    		if(!is_null($parent) || $name != $object->get_name()){ 
	    		$group = new CourseGroup();
		    	$group->set_name($general->get_name()->value());
		    	$group->set_course_code($object->get_id());
		    	$max = $general->get_max_number_of_members()->value();
		    	$max = empty($max) ? null : $max;
		    	$group->set_max_number_of_members($max);
		    	$group->set_description($general->get_description()->value());
		    	$group->set_self_registration_allowed((bool)$general->get_self_reg_allowed()->value());
		    	$group->set_self_unregistration_allowed((bool)$general->get_self_unreg_allowed()->value());
		    	$group->set_parent_id(empty($parent) ? 0 : $parent->get_id());
	    		$group->save();    		
    		}else{
    			$group = null;
    		}
    		$this->process_user_relations($object, $item, $group);
    		$this->process_groups($object, $item, $group);
    	}
    }
    
    protected function process_user_relations(Course $object, ImscpObjectReader $item, $parent = null){
    	$items = $item->get_user_relations()->list_user_relation();
    	foreach($items as $item){
    		$user = $this->process_user($object, $item->get_user());
    		if(!empty($user)){
    			$user_id = $user->get_id();
     			if(!empty($parent)){
    				$relation = new CourseGroupUserRelation();
    				$relation->set_course_group($parent->get_id());
    				$relation->set_user($user_id);
    				$relation->set_course_group($parent->get_id());
    				$relation->create();
    			}else{
    				$general = $item->get_general();
    				$relation = new CourseUserRelation();
    				$relation->set_course($object->get_id());
    				$relation->set_user($user_id);
    				
    				$role = $item->get_general()->get_role()->value();
    				$role = empty($role) ? 0 : $role;
    				$relation->set_role($role);
    				
    				$status = $item->get_general()->get_status()->value();
    				$status = empty($status) ? 0 : $status;
    				$relation->set_status($status);
    				$relation->set_category(0);
    				$relation->save();
    			}
    		}else{
    			//should not be the case unless
    			$log = $this->get_settings()->get_log();
    			$log->error(Translation::Translate('FailedToCreateUser'));	
    		}
    	}
    }
    
    protected function process_user(Course $object, ImscpObjectReader $item){
    	$item = $item->get_general();
    	$store = User::get_data_manager();
    	$user = $store->retrieve_user_by_username($item->get_official_code()->value());
    	$user = empty($user) ? reset($store->retrieve_users_by_email($item->get_email()->value())) : $user;
    	$user = empty($user) ? new User() : $user;
    	
    	if(!$user->is_identified()){
	    	$skip = array(	User::PROPERTY_ID, 
	    					User::PROPERTY_CREATOR_ID,
	    					User::PROPERTY_REGISTRATION_DATE);
	    						
	    	$names = array_diff(User::get_default_property_names(), $skip);
	    	$properties = $this->read_properties($item, $names);
	    	foreach($properties as $name=>$value){
	    		$user->set_default_property($name, $value);
	    	}
    		$user->set_creator_id($this->get_settings()->get_user()->get_id());
    		$user->set_password(uniqid());
    		$user->save();
    	}
    	return $user;
    } 
    
    protected function process_categories(Course $object, ImscpObjectReader $item){
    	$category_name = $item->get_categories()->get_category()->get_name()->value();
    	$category = $this->get_course_category($category_name);
    	if(!empty($category)){
    		$object->set_category($category->get_id());
    	}
    }    
    
    protected function get_course_category($name){
    	if(empty($name)){
    		return 0;
    	}
    	$condition = new EqualityCondition(CourseCategory::PROPERTY_NAME, $name);
    	$store = WeblcmsDataManager::get_instance();
        $result = $store->retrieve_object(CourseCategory::get_table_name(), $condition);
        $result = empty($result) ? $this->create_course_category($name) : $result;
        return $result;
    }
    
    protected function create_course_category($name){
    	$result = new CourseCategory();
    	$result->set_name($name);
    	$result->set_parent(0);
    	$result->save();
    	return $result;
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
    
    /*
    protected function node_to_object($item){
    	$result = $item->node_to_object($item);
    	$result->general->created = $this->parse_date($result->general->created);
    	$result->general->modified = $this->parse_date($result->general->modified);
    	return $result;
    }
*/
    /*
    public static function retrieve_content_object($id){
        return RepositoryDataManager :: get_instance()->retrieve_content_object($id);
    }*/
    
}






?>