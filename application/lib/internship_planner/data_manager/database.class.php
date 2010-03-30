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
		// Update all necessary left-values
	//        $condition = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
	//
	//        $query = 'UPDATE ' . $this->database->escape_table_name('category') . ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($number_of_elements * 2);
	//        if (isset($condition))
	//        {
	//            $translator = new ConditionTranslator($this->database);
	//            $query .= $translator->render_query($condition);
	//        }
	//
	//		$res = $this->query($query);
	//		$res->free();
	//
	//        // Update all necessary right-values
	//        $condition = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
	//        $query = 'UPDATE ' . $this->database->escape_table_name('category') . ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($number_of_elements * 2);
	//
	//        $translator = new ConditionTranslator($this->database);
	//        $query .= $translator->render_query($condition);
	//
	//        $res = $this->query($query);
	//        $res->free();
	//        // TODO: For now we just return true ...
	//        return true;
	}

	function delete_internship_planner_category_nested_values($node, $previous_visited, $number_of_elements, $condition) {
		return $this->database->add_nested_values ( $node, $previous_visited, $number_of_elements, $condition );
		//        $delta = $category->get_right_value() - $category->get_left_value() + 1;
	//
	//        // Update all necessary nested-values
	//        $condition = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $category->get_left_value());
	//
	//        $query = 'UPDATE ' . $this->database->escape_table_name('category');
	//        $query .= ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . ' - ' . $this->quote($delta) . ', ';
	//        $query .= $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);
	//
	//        $translator = new ConditionTranslator($this->database);
	//        $query .= $translator->render_query($condition);
	//
	//        $res = $this->query($query);
	//        $res->free();
	//
	//        // Update some more nested-values
	//        $conditions = array();
	//        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $category->get_left_value());
	//        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $category->get_right_value());
	//        $condition = new AndCondition($conditions);
	//
	//        $query = 'UPDATE ' . $this->database->escape_table_name('category');
	//        $query .= ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);
	//
	//        $translator = new ConditionTranslator($this->database);
	//        $query .= $translator->render_query($condition);
	//
	//        $res = $this->query($query);
	//        $res->free();
	//        return true;
	}

	function move_internship_planner_category($category, $new_parent_id, $new_previous_id = 0, $condition) {
		return $this->database->move ( $category, $new_parent_id, $new_previous_id, $condition );
		// Check some things first to avoid trouble
	//        if ($new_previous_id)
	//        {
	//            // Don't let people move an element behind itself
	//            // No need to spawn an error, since we're just not doing anything
	//            if ($new_previous_id == $category->get_id())
	//            {
	//                return true;
	//            }
	//
	//            $new_previous = $this->retrieve_category($new_previous_id);
	//            // TODO: What if category $new_previous_id doesn't exist ? Return error.
	//            $new_parent_id = $new_previous->get_parent();
	//        }
	//        else
	//        {
	//            // No parent ID was set ... problem !
	//            if ($new_parent_id == 0)
	//            {
	//                return false;
	//            }
	//            // Move the category underneath one of it's children ?
	//            // I think not ... Return error
	//            if ($category->is_parent_of($new_parent_id))
	//            {
	//                return false;
	//            }
	//            // Move an element underneath itself ?
	//            // No can do ... just ignore and return true
	//            if ($new_parent_id == $category->get_id())
	//            {
	//                return true;
	//            }
	//            // Try to retrieve the data of the parent element
	//            $new_parent = $this->retrieve_category($new_parent_id);
	//            // TODO: What if this is an invalid category ? Return error.
	//        }
	//
	//        $number_of_elements = ($category->get_right_value() - $category->get_left_value() + 1) / 2;
	//        $previous_visited = $new_previous_id ? $new_previous->get_right_value() : $new_parent->get_left_value();
	//
	//        // Update the nested values so we can actually add the element
	//        // Return false if this failed
	//        if (! $this->add_nested_values($previous_visited, $number_of_elements))
	//        {
	//            return false;
	//        }
	//
	//        // Now we can update the actual parent_id
	//        // Return false if this failed
	//        $category = $this->retrieve_category($category->get_id());
	//        $category->set_parent($new_parent_id);
	//        if (! $category->update())
	//        {
	//            return false;
	//        }
	//
	//        // Update the left/right values of those elements that are being moved
	//
	//
	//        // First get the offset we need to add to the left/right values
	//        // if $newPrevId is given we need to get the right value,
	//        // otherwise the left since the left/right has changed
	//        // because we already updated it up there. We need to get them again.
	//        // We have to do that anyway, to have the proper new left/right values
	//        if ($new_previous_id)
	//        {
	//            $temp = $this->retrieve_category($new_previous_id);
	//            // TODO: What if $temp doesn't exist ? Return error.
	//            $calculate_width = $temp->get_right_value();
	//        }
	//        else
	//        {
	//            $temp = $this->retrieve_category($new_parent_id);
	//            // TODO: What if $temp doesn't exist ? Return error.
	//            $calculate_width = $temp->get_left_value();
	//        }
	//
	//        // Get the element that is being moved again, since the left and
	//        // right might have changed by the add-call
	//
	//
	//        $category = $this->retrieve_category($category->get_id());
	//        // TODO: What if $category doesn't exist ? Return error.
	//
	//        // Calculate the offset of the element to to the spot where it should go
	//        // correct the offset by one, since it needs to go inbetween!
	//        $offset = $calculate_width - $category->get_left_value() + 1;
	//
	//        // Do the actual update
	//        $conditions = array();
	//        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, ($category->get_left_value() - 1));
	//        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, ($category->get_right_value() + 1));
	//        $condition = new AndCondition($conditions);
	//
	//        $query = 'UPDATE ' . $this->database->escape_table_name('category');
	//        $query .= ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($offset) . ', ';
	//        $query .= $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($offset);
	//
	//        $translator = new ConditionTranslator($this->database);
	//        $query .= $translator->render_query($condition);
	//
	//        $res = $this->query($query);
	//        $res->free();
	//
	//        // Remove the subtree where the category was before
	//        if (! $this->delete_nested_values($category))
	//        {
	//            return false;
	//        }
	//
	//        return true;
	}

	function count_internship_planner_category_children($node, $condition) {
		return $this->database->count_children ( $node, $condition );
	}

	function get_internship_planner_category_children($node, $recursieve, $condition) {
		return $this->database->get_children ( $node, $recursieve, $condition );
	}

	function count_internship_planner_category_siblings($node, $include_object, $condition) {
		return $this->database->count_siblings ( $node, $include_object, $condition );
	}

	function get_internship_planner_category_siblings($node, $include_object, $condition) {
		return $this->database->get_siblings ( $node, $include_object, $condition );
	}

	function count_internship_planner_category_parents($node, $include_object, $condition) {
		return $this->database->count_parents ( $node, $include_object, $condition );
	}

	function get_internship_planner_category_parents($node, $recursieve, $include_object, $condition) {
		return $this->database->get_parents ( $node, $recursieve, $include_object, $condition );
	}

//	function retrieve_category_root($id)
// 	{
// 		$conditions = array();
// 		$conditions[] = new EqualityCondition(CourseInternshipPlannerCategory :: PROPERTY_PARENT_ID, 0);
// 		$condition = new AndCondition($conditions);
// 		return $this->retrieve_categories($condition)->next_result();
// 	}


}
?>