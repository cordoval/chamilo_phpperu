<?php

/**
 * Facade for chamilo
 * @author lo
 *
 */
/*class CpChamilo {
	
	public static function get_category_by_name($parent_id, $name){
		$condition = array();
		$condition[] = new EqualityCondition(RepositoryCategory :: PROPERTY_NAME, $name);
		$condition[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $parent_id);
		$condition = new AndCondition($condition);
        $categories = RepositoryDataManager::get_instance()->retrieve_categories($condition);
        if($categories->size() > 0){
         	return $categories->next_result(); 
         }else{
         	return null;
         }
	}
	
	public static function category_exists($parent_id, $name){
		return is_null(self::get_category_by_name($parent_id, $name));
	}
}*/
 
?>