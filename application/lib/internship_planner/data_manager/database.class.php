<?php
/**
 * @package internship_planner.datamanager
 */
//require_once dirname ( __FILE__ ) . '/../category.class.php';
require_once dirname ( __FILE__ ) . '/../location.class.php';
//require_once dirname ( __FILE__ ) . '/../location_category.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_category.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_mentor.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_moment.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_type.class.php';
//require_once dirname ( __FILE__ ) . '/../mentor.class.php';
//require_once dirname ( __FILE__ ) . '/../moment.class.php';
//require_once dirname ( __FILE__ ) . '/../period.class.php';
require_once dirname ( __FILE__ ) . '/../organisation.class.php';
require_once 'MDB2.php';

class DatabaseInternshipPlannerDataManager extends InternshipPlannerDataManager {
	private $database;
	
	function initialize() {
		$this->database = new NestedTreeDatabase ();
//		$this->database->set_prefix ( 'internship_planner_' );
	

	}
	
	function create_storage_unit($name, $properties, $indexes) {
		return $this->database->create_storage_unit ( $name, $properties, $indexes );
	}
	
	//internship planner locations
	

	function create_internship_location($location) {
		return $this->database->create ( $location );
	}
	
	function update_internship_location($location) {
		$condition = new EqualityCondition ( InternshipLocation::PROPERTY_ID, $location->get_id () );
		return $this->database->update ( $location, $condition );
	}
	
	function delete_internship_location($location) {
		$condition = new EqualityCondition ( InternshipLocation::PROPERTY_ID, $location->get_id () );
		return $this->database->delete ( $location->get_table_name (), $condition );
	}
	
	function count_locations($condition = null) {
		return $this->database->count_objects ( InternshipLocation::get_table_name (), $condition );
	}
	
	function retrieve_location($id) {
		$condition = new EqualityCondition ( InternshipLocation::PROPERTY_ID, $id );
		return $this->database->retrieve_object ( InternshipLocation::get_table_name (), $condition, null, InternshipLocation::CLASS_NAME );
	}
	
	function retrieve_locations($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->database->retrieve_objects ( InternshipLocation::get_table_name (), $condition, $offset, $max_objects, $order_by, InternshipLocation::CLASS_NAME );
	}
	
	//internship planner organisations
	

	function create_internship_organisation($organisation) {
		return $this->database->create ( $organisation );
	}
	
	function update_internship_organisation($organisation) {
		$condition = new EqualityCondition ( InternshipOrganisation::PROPERTY_ID, $organisation->get_id () );
		return $this->database->update ( $organisation, $condition );
	}
	
	function delete_internship_organisation($organisation) {
		$condition = new EqualityCondition ( InternshipOrganisation::PROPERTY_ID, $organisation->get_id () );
		return $this->database->delete ( $organisation->get_table_name (), $condition );
	}
	
	function count_organisations($condition = null) {
		return $this->database->count_objects ( InternshipOrganisation::get_table_name (), $condition );
	}
	
	function retrieve_organisation($id) {
		$condition = new EqualityCondition ( InternshipOrganisation::PROPERTY_ID, $id );
		return $this->database->retrieve_object ( InternshipOrganisation::get_table_name (), $condition, null, InternshipOrganisation::CLASS_NAME );
	}
	
	function retrieve_organisations($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->database->retrieve_objects ( InternshipOrganisation::get_table_name (), $condition, $offset, $max_objects, $order_by, InternshipOrganisation::CLASS_NAME );
	}
	
	//internship planner categories
	

	function update_internship_planner_category($category) {
		$condition = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_ID, $category->get_id () );
		return $this->database->update ( $category, $condition );
	}
	
	function delete_internship_planner_category($category) {
		$condition = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_ID, $category->get_id () );
		$bool = $this->database->delete ( $category->get_table_name (), $condition );
		
		$condition_subcategories = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_PARENT_ID, $category->get_id () );
		$categories = $this->retrieve_categories ( $condition_subcategories );
		while ( $gr = $categories->next_result () ) {
			$bool = $bool & $this->delete_category ( $gr );
		}
		
		$this->truncate_category ( $category );
		
		return $bool;
	
	}
	
	function truncate_category($category) {
		$condition = new EqualityCondition ( InternshipPlannerCategoryRelLocation::PROPERTY_CATEGORY_ID, $category->get_id () );
		return $this->database->delete ( InternshipPlannerCategoryRelLocation::get_table_name (), $condition );
	}
	
	function delete_category_rel_location($categoryrellocation) {
		$conditions = array ();
		$conditions [] = new EqualityCondition ( InternshipPlannerCategoryRelLocation::PROPERTY_CATEGORY_ID, $categoryrellocation->get_category_id () );
		$conditions [] = new EqualityCondition ( InternshipPlannerCategoryRelLocation::PROPERTY_LOCATION_ID, $categoryrellocation->get_location_id () );
		$condition = new AndCondition ( $conditions );
		
		return $this->database->delete ( $categoryrellocation->get_table_name (), $condition );
	}
	
	function create_internship_planner_category($category) {
		return $this->database->create ( $category );
	}
	
	function create_internship_planner_category_rel_location($categoryrellocation) {
		return $this->database->create ( $categoryrellocation );
	}
	
	function count_categories($condition = null) {
		return $this->database->count_objects ( InternshipPlannerCategory::get_table_name (), $condition );
	}
	
	function retrieve_categories($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->database->retrieve_objects ( InternshipPlannerCategory::get_table_name (), $condition, $offset, $max_objects, $order_by );
	}
	
	function retrieve_internship_planner_category($id) {
		$condition = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_ID, $id );
		return $this->database->retrieve_object ( InternshipPlannerCategory::get_table_name (), $condition );
	}
	
	function count_category_rel_locations($condition = null) {
		return $this->database->count_objects ( InternshipPlannerCategoryRelLocation::get_table_name (), $condition );
	}
	
	function retrieve_category_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->database->retrieve_objects ( InternshipPlannerCategoryRelLocation::get_table_name (), $condition, $offset, $max_objects, $order_by );
	}
	
	function retrieve_category_rel_location($location_id, $category_id) {
		$conditions = array ();
		$conditions [] = new EqualityCondition ( InternshipPlannerCategoryRelLocation::PROPERTY_LOCATION_ID, $location_id );
		$conditions [] = new EqualityCondition ( InternshipPlannerCategoryRelLocation::PROPERTY_CATEGORY_ID, $category_id );
		$condition = new AndCondition ( $conditions );
		return $this->database->retrieve_object( InternshipPlannerCategoryRelLocation::get_table_name (), $condition );
	}
	
	function retrieve_location_categories($location_id) {
		$condition = new EqualityCondition ( InternshipPlannerCategoryRelLocation::PROPERTY_LOCATION_ID, $location_id );
		return $this->database->retrieve_objects ( InternshipPlannerCategoryRelLocation::get_table_name (), $condition );
	}
	
	function retrieve_category_by_name($name) {
		$condition = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_NAME, $name );
		return $this->database->retrieve_object ( InternshipPlannerCategory::get_table_name (), $condition );
	}
	
	function is_categoryname_available($categoryname, $category_id = null) {
		$condition = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_NAME, $categoryname );
		
		if ($category_id) {
			$conditions = array ();
			$conditions [] = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_NAME, $categoryname );
			$conditions = new EqualityCondition ( InternshipPlannerCategory::PROPERTY_ID, $category_id );
			$condition = new AndCondition ( $conditions );
		}
		
		return ! ($this->database->count_objects ( InternshipPlannerCategory::get_table_name (), $condition ) == 1);
	}
	
	function add_internship_planner_category_nested_values($node, $previous_visited, $number_of_elements = 1, $condition) {
		
		return $this->database->add_nested_values ( $node, $previous_visited, $number_of_elements, $condition );
	}
	
	function delete_internship_planner_category_nested_values($node, $previous_visited, $number_of_elements, $condition) {
		return $this->database->add_nested_values ( $node, $previous_visited, $number_of_elements, $condition );
	}
	
	function move_internship_planner_category($category, $new_parent_id, $new_previous_id = 0, $condition) {
		return $this->database->move ( $category, $new_parent_id, $new_previous_id, $condition );
	}
	
	function count_internship_planner_category_children($node, $condition) {
		return $this->database->count_children ( $node, $condition );
	}
	
	function get_internship_planner_category_children($node, $recursieve, $condition) {
		return $this->database->get_children ( $node, $recursieve, $condition );
	}
	
	function count_internship_planner_category_sibblings($node, $include_object, $condition) {
		return $this->database->count_sibblings ( $node, $include_object, $condition );
	}
	
	function get_internship_planner_category_sibblings($node, $include_object, $condition) {
		return $this->database->get_sibblings ( $node, $include_object, $condition );
	}
	
	function count_internship_planner_category_parents($node, $include_object, $condition) {
		return $this->database->count_parents ( $node, $include_object, $condition );
	}
	
	function get_internship_planner_category_parents($node, $recursieve, $include_object, $condition) {
		return $this->database->get_parents ( $node, $recursieve, $include_object, $condition );
	}
	
	function retrieve_root_category()
 	{
 		$conditions = array();
 		$conditions[] = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_PARENT_ID, 0);
 		$condition = new AndCondition($conditions);
 		$root_category = $this->retrieve_categories($condition)->next_result();
 		if(! isset($root_category)){
 			$root_category = new InternshipPlannerCategory();
 			$root_category->set_name('ROOT');
        	$root_category->set_parent_id(0);
        	$root_category->create();
 		}
 		return $root_category;
 	}


}
?>