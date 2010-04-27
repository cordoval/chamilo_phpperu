<?php
/**
 * @package internship_organizer.datamanager
 */

require_once dirname ( __FILE__ ) . '/../internship_organizer_data_manager.interface.php';

require_once dirname ( __FILE__ ) . '/../category.class.php';
require_once dirname ( __FILE__ ) . '/../location.class.php';
require_once dirname ( __FILE__ ) . '/../category_rel_location.class.php';
require_once dirname ( __FILE__ ) . '/../organisation.class.php';
require_once dirname ( __FILE__ ) . '/../agreement.class.php';
require_once dirname ( __FILE__ ) . '/../moment.class.php';
require_once dirname ( __FILE__ ) . '/../region.class.php';

require_once 'MDB2.php';

class DatabaseInternshipOrganizerDataManager extends Database implements InternshipOrganizerDataManagerInterface {
		
	function initialize() {
		parent :: initialize();
		$this->set_prefix ( 'internship_organizer_' );
	

	}
	
//	function create_storage_unit($name, $properties, $indexes) {
//		return $this->create_storage_unit ( $name, $properties, $indexes );
//	}
	
	//internship planner locations
	

	function create_internship_organizer_location($location) {
		return $this->create ( $location );
	}
	
	function update_internship_organizer_location($location) {
		$condition = new EqualityCondition ( InternshipOrganizerLocation::PROPERTY_ID, $location->get_id () );
		return $this->update ( $location, $condition );
	}
	
	function delete_internship_organizer_location($location) {
		$condition = new EqualityCondition ( InternshipOrganizerLocation::PROPERTY_ID, $location->get_id () );
		return $this->delete ( $location->get_table_name (), $condition );
	}
	
	function count_locations($condition = null) {
		return $this->count_objects ( InternshipOrganizerLocation::get_table_name (), $condition );
	}
	
	function retrieve_location($id) {
		$condition = new EqualityCondition ( InternshipOrganizerLocation::PROPERTY_ID, $id );
		return $this->retrieve_object ( InternshipOrganizerLocation::get_table_name (), $condition, array(), InternshipOrganizerLocation::CLASS_NAME );
	}
	
	function retrieve_locations($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerLocation::get_table_name (), $condition, $offset, $max_objects, $order_by, InternshipOrganizerLocation::CLASS_NAME );
	}
	
	//internship planner organisations
	

	function create_internship_organizer_organisation($organisation) {
		return $this->create ( $organisation );
	}
	
	function update_internship_organizer_organisation($organisation) {
		$condition = new EqualityCondition ( InternshipOrganizerOrganisation::PROPERTY_ID, $organisation->get_id () );
		return $this->update ( $organisation, $condition );
	}
	
	function delete_internship_organizer_organisation($organisation) {
		$condition = new EqualityCondition ( InternshipOrganizerOrganisation::PROPERTY_ID, $organisation->get_id () );
		return $this->delete ( $organisation->get_table_name (), $condition );
	}
	
	function count_organisations($condition = null) {
		return $this->count_objects ( InternshipOrganizerOrganisation::get_table_name (), $condition );
	}
	
	function retrieve_organisation($id) {
		$condition = new EqualityCondition ( InternshipOrganizerOrganisation::PROPERTY_ID, $id );
		return $this->retrieve_object ( InternshipOrganizerOrganisation::get_table_name (), $condition, array(), InternshipOrganizerOrganisation::CLASS_NAME );
	}
	
	function retrieve_organisations($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerOrganisation::get_table_name (), $condition, $offset, $max_objects, $order_by, InternshipOrganizerOrganisation::CLASS_NAME );
	}
	
	//internship planner categories
	

	function update_internship_organizer_category($category) {
		$condition = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_ID, $category->get_id () );
		return $this->update ( $category, $condition );
	}
	
	function delete_internship_organizer_category($category) {
		$condition = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_ID, $category->get_id () );
		$bool = $this->delete ( $category->get_table_name (), $condition );
		
		$condition_subcategories = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_PARENT_ID, $category->get_id () );
		$categories = $this->retrieve_categories ( $condition_subcategories );
		while ( $gr = $categories->next_result () ) {
			$bool = $bool & $this->delete_internship_organizer_category ( $gr );
		}
		
		$this->truncate_category ( $category );
		
		return $bool;
	
	}
	
	function truncate_category($category) {
		$condition = new EqualityCondition ( InternshipOrganizerCategoryRelLocation::PROPERTY_CATEGORY_ID, $category->get_id () );
		return $this->delete ( InternshipOrganizerCategoryRelLocation::get_table_name (), $condition );
	}
	
	function delete_category_rel_location($categoryrellocation) {
		$conditions = array ();
		$conditions [] = new EqualityCondition ( InternshipOrganizerCategoryRelLocation::PROPERTY_CATEGORY_ID, $categoryrellocation->get_category_id () );
		$conditions [] = new EqualityCondition ( InternshipOrganizerCategoryRelLocation::PROPERTY_LOCATION_ID, $categoryrellocation->get_location_id () );
		$condition = new AndCondition ( $conditions );
		
		return $this->delete ( $categoryrellocation->get_table_name (), $condition );
	}
	
	function create_internship_organizer_category($category) {
		return $this->create ( $category );
	}
	
	function create_internship_organizer_category_rel_location($categoryrellocation) {
		return $this->create ( $categoryrellocation );
	}
	
	function count_categories($condition = null) {
		return $this->count_objects ( InternshipOrganizerCategory::get_table_name (), $condition );
	}
	
	function retrieve_categories($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerCategory::get_table_name (), $condition, $offset, $max_objects, $order_by , InternshipOrganizerCategory :: CLASS_NAME);
	}
	
	function retrieve_internship_organizer_category($id) {
		$condition = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_ID, $id );
		return $this->retrieve_object ( InternshipOrganizerCategory::get_table_name (), $condition , array() ,InternshipOrganizerCategory :: CLASS_NAME);
	}
	
	function retrieve_full_category_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rel_alias = $this->get_alias(InternshipOrganizerCategoryRelLocation :: get_table_name());
    	
        $category_alias = $this->get_alias(InternshipOrganizerCategory :: get_table_name());
        $organisation_alias = $this->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = $this->get_alias(InternshipOrganizerLocation :: get_table_name());

        $query = 'SELECT ' . $category_rel_location_alias . ' * ';
        $query .= ' FROM ' . $this->escape_table_name(InternshipOrganizerCategoryRelLocation :: get_table_name()) . ' AS ' . $rel_alias;
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerLocation :: get_table_name()) . ' AS ' . $location_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $rel_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ID, $location_alias);
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerOrganisation :: get_table_name()) . ' AS ' . $organisation_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation_alias);

        return $this->retrieve_object_set($query, InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganizerCategoryRelLocation :: CLASS_NAME);
    }

    function count_full_category_rel_locations($condition = null)
    {
        $rel_alias = $this->get_alias(InternshipOrganizerCategoryRelLocation :: get_table_name());
    	
        $category_alias = $this->get_alias(InternshipOrganizerCategory :: get_table_name());
        $organisation_alias = $this->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = $this->get_alias(InternshipOrganizerLocation :: get_table_name());

        $query = 'SELECT ' . $category_rel_location_alias . ' * ';
        $query .= ' FROM ' . $this->escape_table_name(InternshipOrganizerCategoryRelLocation :: get_table_name()) . ' AS ' . $rel_alias;
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerLocation :: get_table_name()) . ' AS ' . $location_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerCategoryRelLocation :: PROPERTY_LOCATION_ID, $rel_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ID, $location_alias);
        $query .= ' JOIN ' . $this->escape_table_name(InternshipOrganizerOrganisation :: get_table_name()) . ' AS ' . $organisation_alias . ' ON ' . $this->escape_column_name(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $location_alias) . ' = ' . $this->escape_column_name(InternshipOrganizerOrganisation :: PROPERTY_ID, $organisation_alias);
        
        
        
        return $this->count_result_set($query, InternshipOrganizerCategoryRelLocation :: get_table_name(), $condition);
    }
    
	function count_category_rel_locations($condition = null) {
		return $this->count_objects ( InternshipOrganizerCategoryRelLocation::get_table_name (), $condition );
	}
	
	function retrieve_category_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerCategoryRelLocation::get_table_name (), $condition, $offset, $max_objects, $order_by , InternshipOrganizerCategoryRelLocation :: CLASS_NAME);
	}
	
	function retrieve_category_rel_location($location_id, $category_id) {
		$conditions = array ();
		$conditions [] = new EqualityCondition ( InternshipOrganizerCategoryRelLocation::PROPERTY_LOCATION_ID, $location_id );
		$conditions [] = new EqualityCondition ( InternshipOrganizerCategoryRelLocation::PROPERTY_CATEGORY_ID, $category_id );
		$condition = new AndCondition ( $conditions );
		return $this->retrieve_object( InternshipOrganizerCategoryRelLocation::get_table_name (), $condition , array(), InternshipOrganizerCategoryRelLocation :: CLASS_NAME);
	}

	function retrieve_category_by_name($name) {
		$condition = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_NAME, $name );
		return $this->retrieve_object ( InternshipOrganizerCategory::get_table_name (), $condition );
	}
	
	function is_categoryname_available($categoryname, $category_id = null) {
		$condition = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_NAME, $categoryname );
		
		if ($category_id) {
			$conditions = array ();
			$conditions [] = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_NAME, $categoryname );
			$conditions = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_ID, $category_id );
			$condition = new AndCondition ( $conditions );
		}
		
		return ! ($this->count_objects ( InternshipOrganizerCategory::get_table_name (), $condition ) == 1);
	}
	
//	function add_internship_organizer_category_nested_values($node, $previous_visited, $number_of_elements = 1, $condition) {
//		
//		return $this->add_nested_values ( $node, $previous_visited, $number_of_elements, $condition );
//	}
//	
//	function delete_internship_organizer_category_nested_values($node, $previous_visited, $number_of_elements, $condition) {
//		return $this->add_nested_values ( $node, $previous_visited, $number_of_elements, $condition );
//	}
//	
//	function move_internship_organizer_category($category, $new_parent_id, $new_previous_id = 0, $condition) {
//		return $this->move ( $category, $new_parent_id, $new_previous_id, $condition );
//	}
//	
//	function count_internship_organizer_category_children($node, $condition) {
//		return $this->count_children ( $node, $condition );
//	}
//	
//	function get_internship_organizer_category_children($node, $recursieve, $condition) {
//		return $this->get_children ( $node, $recursieve, $condition );
//	}
//	
//	function count_internship_organizer_category_siblings($node, $include_object, $condition) {
//		return $this->count_siblings ( $node, $include_object, $condition );
//	}
//	
//	function get_internship_organizer_category_siblings($node, $include_object, $condition) {
//		return $this->get_siblings ( $node, $include_object, $condition );
//	}
//	
//	function count_internship_organizer_category_parents($node, $include_object, $condition) {
//		return $this->count_parents ( $node, $include_object, $condition );
//	}
//	
//	function get_internship_organizer_category_parents($node, $recursieve, $include_object, $condition) {
//		return $this->get_parents ( $node, $recursieve, $include_object, $condition );
//	}
	
	function retrieve_root_category()
 	{
 		$conditions = array();
 		$conditions[] = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_PARENT_ID, 0);
 		$condition = new AndCondition($conditions);
 		$root_category = $this->retrieve_categories($condition)->next_result();
 		if(! isset($root_category)){
 			$root_category = new InternshipOrganizerCategory();
 			$root_category->set_name('ROOT');
        	$root_category->set_parent_id(0);
        	$root_category->create();
 		}
 		return $root_category;
 	}
	
 	//internship planner moments
 	
 	function create_internship_organizer_moment($moment) {
		return $this->create ( $moment );
	}
	
	function update_internship_organizer_moment($moment) {
		$condition = new EqualityCondition ( InternshipOrganizerMoment::PROPERTY_ID, $moment->get_id () );
		return $this->update ( $moment, $condition );
	}
	
	function delete_internship_organizer_moment($moment) {
		$condition = new EqualityCondition ( InternshipOrganizerMoment::PROPERTY_ID, $moment->get_id () );
		return $this->delete ( $moment->get_table_name (), $condition );
	}
	
	function count_moments($condition = null) {
		return $this->count_objects ( InternshipOrganizerMoment::get_table_name (), $condition );
	}
	
	function retrieve_moment($id) {
		$condition = new EqualityCondition ( InternshipOrganizerMoment::PROPERTY_ID, $id );
		return $this->retrieve_object ( InternshipOrganizerMoment::get_table_name (), $condition, array(), InternshipOrganizerMoment::CLASS_NAME );
	}
	
	function retrieve_moments($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerMoment::get_table_name (), $condition, $offset, $max_objects, $order_by, InternshipOrganizerMoment::CLASS_NAME );
	}
	
	//internship planner agreements
	

	function create_internship_organizer_agreement($agreement) {
		return $this->create ( $agreement );
	}
	
	function update_internship_organizer_agreement($agreement) {
		$condition = new EqualityCondition ( InternshipOrganizerAgreement::PROPERTY_ID, $agreement->get_id () );
		return $this->update ( $agreement, $condition );
	}
	
	function delete_internship_organizer_agreement($agreement) {
		$condition = new EqualityCondition ( InternshipOrganizerAgreement::PROPERTY_ID, $agreement->get_id () );
		return $this->delete ( $agreement->get_table_name (), $condition );
	}
	
	function count_agreements($condition = null) {
		return $this->count_objects ( InternshipOrganizerAgreement::get_table_name (), $condition );
	}
	
	function retrieve_agreement($id) {
		$condition = new EqualityCondition ( InternshipOrganizerAgreement::PROPERTY_ID, $id );
		return $this->retrieve_object ( InternshipOrganizerAgreement::get_table_name (), $condition, array(), InternshipOrganizerAgreement::CLASS_NAME );
	}
	
	function retrieve_agreements($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerAgreement::get_table_name (), $condition, $offset, $max_objects, $order_by, InternshipOrganizerAgreement::CLASS_NAME );
	}
	
//internship planner regions##
	

	function update_internship_organizer_region($region) {
		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_ID, $region->get_id () );
		return $this->update ( $region, $condition );
	}
	
	function delete_internship_organizer_region($region) {
		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_ID, $region->get_id () );
		$bool = $this->delete ( $region->get_table_name (), $condition );
		
		$condition_subregions = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_PARENT_ID, $region->get_id () );
		$regions = $this->retrieve_regions ( $condition_subregions );
		while ( $gr = $regions->next_result () ) {
			$bool = $bool & $this->delete_internship_organizer_region ( $gr );
			//mag dit? (i.e. recursieve oproep)
		}
		
		return $bool;
	
	}
//	
//	function truncate_region($region) {
//		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_ID, $region->get_id () );
//		return $this->delete ( InternshipOrganizerRegion::get_table_name (), $condition );
//	}
//	
	function create_internship_organizer_region($region) {
		return $this->create ( $region );
	}
	
	function count_regions($condition = null) {
		return $this->count_objects ( InternshipOrganizerRegion::get_table_name (), $condition );
	}
	
	function retrieve_regions($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerRegion::get_table_name (), $condition, $offset, $max_objects, $order_by , InternshipOrganizerRegion :: CLASS_NAME);
	}
	
	function retrieve_internship_organizer_region($id) {
		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_ID, $id );
		return $this->retrieve_object ( InternshipOrganizerRegion::get_table_name (), $condition , array() ,InternshipOrganizerRegion :: CLASS_NAME);
	}

	function retrieve_region_by_name($name) {
		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_NAME, $name );
		return $this->retrieve_object ( InternshipOrganizerRegion::get_table_name (), $condition );
	}
	
	function is_regionname_available($regionname, $region_id = null) {
		$condition = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_NAME, $regionname );
		
		if ($region_id) {
			$conditions = array ();
			$conditions [] = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_NAME, $regionname );
			$conditions = new EqualityCondition ( InternshipOrganizerRegion::PROPERTY_ID, $region_id );
			$condition = new AndCondition ( $conditions );
		}
		
		return ! ($this->count_objects ( InternshipOrganizerRegion::get_table_name (), $condition ) == 1);
	}
	
//	function add_internship_organizer_region_nested_values($node, $previous_visited, $number_of_elements = 1, $condition) {
//		
//		return $this->add_nested_values ( $node, $previous_visited, $number_of_elements, $condition );
//	}
//	
//	function delete_internship_organizer_region_nested_values($node, $previous_visited, $number_of_elements, $condition) {
//		return $this->add_nested_values ( $node, $previous_visited, $number_of_elements, $condition );
//	}
//	
//	function count_internship_organizer_region_children($node, $condition) {
//		return $this->count_children ( $node, $condition );
//	}
//	
//	function get_internship_organizer_region_children($node, $recursieve, $condition) {
//		return $this->get_children ( $node, $recursieve, $condition );
//	}
//	
//	function count_internship_organizer_region_siblings($node, $include_object, $condition) {
//		return $this->count_siblings ( $node, $include_object, $condition );
//	}
//	
//	function get_internship_organizer_region_siblings($node, $include_object, $condition) {
//		return $this->get_siblings ( $node, $include_object, $condition );
//	}
//	
//	function count_internship_organizer_region_parents($node, $include_object, $condition) {
//		return $this->count_parents ( $node, $include_object, $condition );
//	}
//	
//	function get_internship_organizer_region_parents($node, $recursieve, $include_object, $condition) {
//		return $this->get_parents ( $node, $recursieve, $include_object, $condition );
//	}
	
	function retrieve_root_region()
 	{
// 		$conditions = array();
 		$condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_PARENT_ID, 0);
// 		$condition = new AndCondition($conditions);
 		$root_region = $this->retrieve_regions($condition)->next_result();
 		if(! isset($root_region)){
 			$root_region = new InternshipOrganizerRegion();
 			$root_region->set_name(Translation::get('World'));
        	$root_region->set_parent_id(0);
        	$root_region->create();
        	
 		}
 		return $root_region;
 	}

 	
	//internship planner mentors
	

	function create_internship_organizer_mentor($mentor) {
		return $this->create ( $mentor );
	}
	
	function update_internship_organizer_mentor($mentor) {
		$condition = new EqualityCondition ( InternshipOrganizerMentor::PROPERTY_ID, $mentor->get_id () );
		return $this->update ( $mentor, $condition );
	}
	
	function delete_internship_organizer_mentor($mentor) {
		$condition = new EqualityCondition ( InternshipOrganizerMentor::PROPERTY_ID, $mentor->get_id () );
		return $this->delete ( $mentor->get_table_name (), $condition );
	}
	
	function count_mentors($condition = null) {
		return $this->count_objects ( InternshipOrganizerMentor::get_table_name (), $condition );
	}
	
	function retrieve_mentor($id) {
		$condition = new EqualityCondition ( InternshipOrganizerMentor::PROPERTY_ID, $id );
		return $this->retrieve_object ( InternshipOrganizerMentor::get_table_name (), $condition, array(), InternshipOrganizerMentor::CLASS_NAME );
	}
	
	function retrieve_mentors($condition = null, $offset = null, $max_objects = null, $order_by = null) {
		return $this->retrieve_objects ( InternshipOrganizerMentor::get_table_name (), $condition, $offset, $max_objects, $order_by, InternshipOrganizerMentor::CLASS_NAME );
	}
 	
}
?>