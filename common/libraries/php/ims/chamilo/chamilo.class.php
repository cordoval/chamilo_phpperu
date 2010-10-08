<?php

require_once Path::get_application_path() . 'lib/weblcms/content_object_publication.class.php';
require_once Path::get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';

/**
 * 
 * Facade for chamilo.
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class Chamilo{
	
    public static function get_local_catalogue_name(){
    	return PlatformSetting :: get('institution_url', 'admin');
    }
    
    public static function get_default_property_names(){
    	return ContentObject::get_default_property_names();
    }
    
    public static function retrieve_local_object($catalogue, $id){
    	if(empty($catalogue) || $catalogue == self::get_local_catalogue_name()){
    		return ContentObject::get_by_id($id);	
    	}else{
    		return ContentObjectMetadata::get_by_catalog_entry_values($catalogue, $id);
    	}
    }

    public static function retrieve_children($co){
    	$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $co->get_id(), ComplexContentObjectItem :: get_table_name());
    	$rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_complex_content_object_items($condition);
    }
    
    public static function retrieve_category($id){
    	$condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $id);
    	$rdm = RepositoryDataManager :: get_instance();
    	return $rdm->retrieve_categories($condition)->next_result(); 
    }
    
    public static function retrieve_metadata($co){
    	$id = $co->get_id();
        $condition = new EqualityCondition(ContentObjectMetadata :: PROPERTY_CONTENT_OBJECT, $id);
    	$rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object_metadata($condition);
    }
    
    public static function retrieve_identifiers($co){
    	$result = array();
    	$ids = array();
    	$metas = self::retrieve_metadata($co);
    	while($meta = $metas->next_result()){
    		$property = $meta->get_property();
    		$value = $meta->get_value();
    		$property = str_replace('[', ',', $property);
    		$property = str_replace(']', ',', $property);
    		$property = str_replace(',,', ',', $property);
    		$property = trim($property, ',');
    		$path = explode(",", $property);
    		if($path[0]=='general_identifier'){
    			$index = $path[1];
    			$name = $path[2];
    			$ids[$index][$name] = $value;
    		}
    	}
    	foreach($ids as $id){
    		$result[$id['catalog']]=$id['entry'];
    	}
    	$result[self::get_local_catalogue_name()]=$co->get_id();
    	
    	return $result;
    }

    public static function retrieve_content_object($id){
        return RepositoryDataManager :: get_instance()->retrieve_content_object($id);
    }
    
    public static function get_category_by_name($name, $parent = 0){
    	$condition_1 = new EqualityCondition(RepositoryCategory::PROPERTY_NAME, $name);
    	$condition_2 = new EqualityCondition(RepositoryCategory::PROPERTY_PARENT, $parent);
    	$condition = new AndCondition($condition_1, $condition_2);
    	$store = RepositoryDataManager::get_instance(); // RepositoryCategory::get_data_manager();
        $objects = $store->retrieve_categories($condition);
        while($result = $objects->next_result()){
        	return $result;
        }
        return false;
    }
    
    public static function get_course_content_objects($course_id){
    	$result = array();
        $condition = new EqualityCondition(ContentObjectPublication::PROPERTY_COURSE_ID, $course_id);
        $publications = ContentObjectPublication::get_data_manager()->retrieve_content_object_publications_new($condition);
        while($publication = $publications->next_result()){
        	$id = $publication->get_content_object_id();
        	$result[] = Chamilo::retrieve_content_object($id);
        }
        return $result;
    }

    public static function get_course_publications($course_id){
    	$result = array();
        $condition = new EqualityCondition(ContentObjectPublication::PROPERTY_COURSE_ID, $course_id);
        $publications = ContentObjectPublication::get_data_manager()->retrieve_content_object_publications($condition);
        while($publication = $publications->next_result()){
        	$result[] = $publication;
        }
        return $result;
    }
    
    public static function get_course_user_relations($course_id){
    	$result = array();
        $condition = new EqualityCondition(CourseUserRelation::PROPERTY_COURSE, $course_id);
        $relationships = CourseUserRelation::get_data_manager()->retrieve_course_user_relations($condition);
        while($relationship = $relationships->next_result()){
        	$result[] = $relationship;
        }
        return $result;
    }
    
    public static function retrieve_course($course_id){
    	return WeblcmsDataManager::get_instance()->retrieve_course($course_id);
    }


}







?>