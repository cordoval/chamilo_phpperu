<?php

/**
 * Facade for chamilo.
 * @author lo
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
        $conditions = new EqualityCondition(ContentObjectMetadata :: PROPERTY_CONTENT_OBJECT, $id);
    	$rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object_metadata($conditions);
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
}