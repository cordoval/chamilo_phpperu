<?php
/**
 * @package internship_planner.datamanager
 */
//require_once dirname ( __FILE__ ) . '/../category.class.php';
require_once dirname(__FILE__) . '/../location.class.php';
//require_once dirname ( __FILE__ ) . '/../location_category.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_category.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_mentor.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_moment.class.php';
//require_once dirname ( __FILE__ ) . '/../location_rel_type.class.php';
//require_once dirname ( __FILE__ ) . '/../mentor.class.php';
//require_once dirname ( __FILE__ ) . '/../moment.class.php';
//require_once dirname ( __FILE__ ) . '/../period.class.php';
require_once dirname(__FILE__) . '/../organisation.class.php';
require_once 'MDB2.php';

class DatabaseInternshipPlannerDataManager extends InternshipPlannerDataManager
{
    private $database;

    function initialize()
    {
        $this->database = new Database();
        $this->database->set_prefix('internship_planner_');
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function create_internship_location($location)
    {
        return $this->database->create($location);
    }

    function update_internship_location($location)
    {
        $condition = new EqualityCondition(InternshipLocation :: PROPERTY_ID, $location->get_id());
        return $this->database->update($location, $condition);
    }

    function delete_internship_location($location)
    {
        $condition = new EqualityCondition(InternshipLocation :: PROPERTY_ID, $location->get_id());
        return $this->database->delete($location->get_table_name(), $condition);
    }

    function count_locations($condition = null)
    {
        return $this->database->count_objects(InternshipLocation :: get_table_name(), $condition);
    }

    function retrieve_location($id)
    {
        $condition = new EqualityCondition(InternshipLocation :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(InternshipLocation :: get_table_name(), $condition, null, InternshipLocation :: CLASS_NAME);
    }

    function retrieve_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(InternshipLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipLocation :: CLASS_NAME);
    }

    function create_internship_organisation($organisation)
    {
        return $this->database->create($organisation);
    }

    function update_internship_organisation($organisation)
    {
        $condition = new EqualityCondition(InternshipOrganisation :: PROPERTY_ID, $organisation->get_id());
        return $this->database->update($organisation, $condition);
    }

    function delete_internship_organisation($organisation)
    {
        $condition = new EqualityCondition(InternshipOrganisation :: PROPERTY_ID, $organisation->get_id());
        return $this->database->delete($organisation->get_table_name(), $condition);
    }

    function count_organisations($condition = null)
    {
        return $this->database->count_objects(InternshipOrganisation :: get_table_name(), $condition);
    }

    function retrieve_organisation($id)
    {
        $condition = new EqualityCondition(InternshipOrganisation :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(InternshipOrganisation :: get_table_name(), $condition, null, InternshipOrganisation :: CLASS_NAME);
    }

    function retrieve_organisations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(InternshipOrganisation :: get_table_name(), $condition, $offset, $max_objects, $order_by, InternshipOrganisation :: CLASS_NAME);
    }
      
    function update_category($category)
    {
        $condition = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_ID, $category->get_id());
        return $this->database->update($category, $condition);
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_ID, $category->get_id());
        $bool = $this->database->delete($category->get_table_name(), $condition);

        $condition_subcategories = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_PARENT, $category->get_id());
        $categories = $this->retrieve_categories($condition_subcategories);
        while ($gr = $categories->next_result())
        {
            $bool = $bool & $this->delete_category($gr);
        }

        $this->truncate_category($category);

        return $bool;

    }

    function truncate_category($category)
    {
        $condition = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_GROUP_ID, $category->get_id());
        return $this->database->delete(InternshipPlannerCategoryRelLocation :: get_table_name(), $condition);
    }

    function delete_category_rel_location($categoryrellocation)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_GROUP_ID, $categoryrellocation->get_category_id());
        $conditions[] = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_LOCATION_ID, $categoryrellocation->get_location_id());
        $condition = new AndCondition($conditions);

        return $this->database->delete($categoryrellocation->get_table_name(), $condition);
    }

    function create_category($category)
    {
        return $this->database->create($category);
    }

    function create_category_rel_location($categoryrellocation)
    {
        return $this->database->create($categoryrellocation);
    }

    function count_categories($condition = null)
    {
        return $this->database->count_objects(InternshipPlannerCategory :: get_table_name(), $condition);
    }

    function count_category_rel_locations($condition = null)
    {
        return $this->database->count_objects(InternshipPlannerCategoryRelLocation :: get_table_name(), $condition);
    }

    function retrieve_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(InternshipPlannerCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_category_rel_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(InternshipPlannerCategoryRelLocation :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_category_rel_location($location_id, $category_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_LOCATION_ID, $location_id);
        $conditions[] = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_GROUP_ID, $category_id);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(InternshipPlannerCategoryRelLocation :: get_table_name(), $condition);
    }

    function retrieve_location_categories($location_id)
    {
        $condition = new EqualityCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_LOCATION_ID, $location_id);
        return $this->database->retrieve_objects(InternshipPlannerCategoryRelLocation :: get_table_name(), $condition);
    }

    function retrieve_category($id)
    {
        $condition = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(InternshipPlannerCategory :: get_table_name(), $condition);
    }

    function retrieve_category_by_name($name)
    {
        $condition = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_NAME, $name);
        return $this->database->retrieve_object(InternshipPlannerCategory :: get_table_name(), $condition);
    }

    function is_categoryname_available($categoryname, $category_id = null)
    {
        $condition = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_NAME, $categoryname);

        if ($category_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_NAME, $categoryname);
            $conditions = new EqualityCondition(InternshipPlannerCategory :: PROPERTY_ID, $category_id);
            $condition = new AndCondition($conditions);
        }

        return ! ($this->database->count_objects(InternshipPlannerCategory :: get_table_name(), $condition) == 1);
    }

    function add_nested_values($previous_visited, $number_of_elements = 1)
    {
        // Update all necessary left-values
        $condition = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);

        $query = 'UPDATE ' . $this->database->escape_table_name('category') . ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($number_of_elements * 2);
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        }

		$res = $this->query($query);
		$res->free();

        // Update all necessary right-values
        $condition = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
        $query = 'UPDATE ' . $this->database->escape_table_name('category') . ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($number_of_elements * 2);

        $translator = new ConditionTranslator($this->database);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();
        // TODO: For now we just return true ...
        return true;
    }

    function delete_nested_values($category)
    {
        $delta = $category->get_right_value() - $category->get_left_value() + 1;

        // Update all necessary nested-values
        $condition = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $category->get_left_value());

        $query = 'UPDATE ' . $this->database->escape_table_name('category');
        $query .= ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . ' - ' . $this->quote($delta) . ', ';
        $query .= $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);

        $translator = new ConditionTranslator($this->database);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();

        // Update some more nested-values
        $conditions = array();
        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $category->get_left_value());
        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $category->get_right_value());
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->database->escape_table_name('category');
        $query .= ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);

        $translator = new ConditionTranslator($this->database);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();
        return true;
    }

    function move_category($category, $new_parent_id, $new_previous_id = 0)
    {
        // Check some things first to avoid trouble
        if ($new_previous_id)
        {
            // Don't let people move an element behind itself
            // No need to spawn an error, since we're just not doing anything
            if ($new_previous_id == $category->get_id())
            {
                return true;
            }

            $new_previous = $this->retrieve_category($new_previous_id);
            // TODO: What if category $new_previous_id doesn't exist ? Return error.
            $new_parent_id = $new_previous->get_parent();
        }
        else
        {
            // No parent ID was set ... problem !
            if ($new_parent_id == 0)
            {
                return false;
            }
            // Move the category underneath one of it's children ?
            // I think not ... Return error
            if ($category->is_parent_of($new_parent_id))
            {
                return false;
            }
            // Move an element underneath itself ?
            // No can do ... just ignore and return true
            if ($new_parent_id == $category->get_id())
            {
                return true;
            }
            // Try to retrieve the data of the parent element
            $new_parent = $this->retrieve_category($new_parent_id);
            // TODO: What if this is an invalid category ? Return error.
        }

        $number_of_elements = ($category->get_right_value() - $category->get_left_value() + 1) / 2;
        $previous_visited = $new_previous_id ? $new_previous->get_right_value() : $new_parent->get_left_value();

        // Update the nested values so we can actually add the element
        // Return false if this failed
        if (! $this->add_nested_values($previous_visited, $number_of_elements))
        {
            return false;
        }

        // Now we can update the actual parent_id
        // Return false if this failed
        $category = $this->retrieve_category($category->get_id());
        $category->set_parent($new_parent_id);
        if (! $category->update())
        {
            return false;
        }

        // Update the left/right values of those elements that are being moved


        // First get the offset we need to add to the left/right values
        // if $newPrevId is given we need to get the right value,
        // otherwise the left since the left/right has changed
        // because we already updated it up there. We need to get them again.
        // We have to do that anyway, to have the proper new left/right values
        if ($new_previous_id)
        {
            $temp = $this->retrieve_category($new_previous_id);
            // TODO: What if $temp doesn't exist ? Return error.
            $calculate_width = $temp->get_right_value();
        }
        else
        {
            $temp = $this->retrieve_category($new_parent_id);
            // TODO: What if $temp doesn't exist ? Return error.
            $calculate_width = $temp->get_left_value();
        }

        // Get the element that is being moved again, since the left and
        // right might have changed by the add-call


        $category = $this->retrieve_category($category->get_id());
        // TODO: What if $category doesn't exist ? Return error.

        // Calculate the offset of the element to to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calculate_width - $category->get_left_value() + 1;

        // Do the actual update
        $conditions = array();
        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, ($category->get_left_value() - 1));
        $conditions[] = new InequalityCondition(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, ($category->get_right_value() + 1));
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->database->escape_table_name('category');
        $query .= ' SET ' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($offset) . ', ';
        $query .= $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . '=' . $this->database->escape_column_name(InternshipPlannerCategory :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($offset);

        $translator = new ConditionTranslator($this->database);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();

        // Remove the subtree where the category was before
        if (! $this->delete_nested_values($category))
        {
            return false;
        }

        return true;
    }
    
    
//	function get_next_location_category_id()
//	{
//		return $this->database->get_next_id(InternshipLocationInternshipPlannerCategory :: get_table_name());
//	}
//
//	function create_location_category($location_category)
//	{
//		return $this->database->create($location_category);
//	}
//
//	function update_location_category($location_category)
//	{
//		$condition = new EqualityCondition(InternshipLocationInternshipPlannerCategory :: PROPERTY_ID, $location_category->get_id());
//		return $this->database->update($location_category, $condition);
//	}
//
//	function delete_location_category($location_category)
//	{
//		$condition = new EqualityCondition(InternshipLocationInternshipPlannerCategory :: PROPERTY_ID, $location_category->get_id());
//		return $this->database->delete($location_category->get_table_name(), $condition);
//	}
//
//	function count_location_categories($condition = null)
//	{
//		return $this->database->count_objects(InternshipLocationInternshipPlannerCategory :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_category($id)
//	{
//		$condition = new EqualityCondition(InternshipLocationInternshipPlannerCategory :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(InternshipLocationInternshipPlannerCategory :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(InternshipLocationInternshipPlannerCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_location_rel_category_id()
//	{
//		return $this->database->get_next_id(InternshipLocationRelInternshipPlannerCategory :: get_table_name());
//	}
//
//	function create_location_rel_category($location_rel_category)
//	{
//		return $this->database->create($location_rel_category);
//	}
//
//	function update_location_rel_category($location_rel_category)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelInternshipPlannerCategory :: PROPERTY_ID, $location_rel_category->get_id());
//		return $this->database->update($location_rel_category, $condition);
//	}
//
//	function delete_location_rel_category($location_rel_category)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelInternshipPlannerCategory :: PROPERTY_ID, $location_rel_category->get_id());
//		return $this->database->delete($location_rel_category->get_table_name(), $condition);
//	}
//
//	function count_location_rel_categories($condition = null)
//	{
//		return $this->database->count_objects(InternshipLocationRelInternshipPlannerCategory :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_category($id)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelInternshipPlannerCategory :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(InternshipLocationRelInternshipPlannerCategory :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(InternshipLocationRelInternshipPlannerCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_location_rel_mentor_id()
//	{
//		return $this->database->get_next_id(InternshipLocationRelMentor :: get_table_name());
//	}
//
//	function create_location_rel_mentor($location_rel_mentor)
//	{
//		return $this->database->create($location_rel_mentor);
//	}
//
//	function update_location_rel_mentor($location_rel_mentor)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelMentor :: PROPERTY_ID, $location_rel_mentor->get_id());
//		return $this->database->update($location_rel_mentor, $condition);
//	}
//
//	function delete_location_rel_mentor($location_rel_mentor)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelMentor :: PROPERTY_ID, $location_rel_mentor->get_id());
//		return $this->database->delete($location_rel_mentor->get_table_name(), $condition);
//	}
//
//	function count_location_rel_mentors($condition = null)
//	{
//		return $this->database->count_objects(InternshipLocationRelMentor :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_mentor($id)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelMentor :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(InternshipLocationRelMentor :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_mentors($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(InternshipLocationRelMentor :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_location_rel_moment_id()
//	{
//		return $this->database->get_next_id(InternshipLocationRelMoment :: get_table_name());
//	}
//
//	function create_location_rel_moment($location_rel_moment)
//	{
//		return $this->database->create($location_rel_moment);
//	}
//
//	function update_location_rel_moment($location_rel_moment)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelMoment :: PROPERTY_ID, $location_rel_moment->get_id());
//		return $this->database->update($location_rel_moment, $condition);
//	}
//
//	function delete_location_rel_moment($location_rel_moment)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelMoment :: PROPERTY_ID, $location_rel_moment->get_id());
//		return $this->database->delete($location_rel_moment->get_table_name(), $condition);
//	}
//
//	function count_location_rel_moments($condition = null)
//	{
//		return $this->database->count_objects(InternshipLocationRelMoment :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_moment($id)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelMoment :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(InternshipLocationRelMoment :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_moments($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(InternshipLocationRelMoment :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_location_rel_type_id()
//	{
//		return $this->database->get_next_id(InternshipLocationRelType :: get_table_name());
//	}
//
//	function create_location_rel_type($location_rel_type)
//	{
//		return $this->database->create($location_rel_type);
//	}
//
//	function update_location_rel_type($location_rel_type)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelType :: PROPERTY_ID, $location_rel_type->get_id());
//		return $this->database->update($location_rel_type, $condition);
//	}
//
//	function delete_location_rel_type($location_rel_type)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelType :: PROPERTY_ID, $location_rel_type->get_id());
//		return $this->database->delete($location_rel_type->get_table_name(), $condition);
//	}
//
//	function count_location_rel_types($condition = null)
//	{
//		return $this->database->count_objects(InternshipLocationRelType :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_type($id)
//	{
//		$condition = new EqualityCondition(InternshipLocationRelType :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(InternshipLocationRelType :: get_table_name(), $condition);
//	}
//
//	function retrieve_location_rel_types($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(InternshipLocationRelType :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_mentor_id()
//	{
//		return $this->database->get_next_id(Mentor :: get_table_name());
//	}
//
//	function create_mentor($mentor)
//	{
//		return $this->database->create($mentor);
//	}
//
//	function update_mentor($mentor)
//	{
//		$condition = new EqualityCondition(Mentor :: PROPERTY_ID, $mentor->get_id());
//		return $this->database->update($mentor, $condition);
//	}
//
//	function delete_mentor($mentor)
//	{
//		$condition = new EqualityCondition(Mentor :: PROPERTY_ID, $mentor->get_id());
//		return $this->database->delete($mentor->get_table_name(), $condition);
//	}
//
//	function count_mentors($condition = null)
//	{
//		return $this->database->count_objects(Mentor :: get_table_name(), $condition);
//	}
//
//	function retrieve_mentor($id)
//	{
//		$condition = new EqualityCondition(Mentor :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(Mentor :: get_table_name(), $condition);
//	}
//
//	function retrieve_mentors($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(Mentor :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_moment_id()
//	{
//		return $this->database->get_next_id(Moment :: get_table_name());
//	}
//
//	function create_moment($moment)
//	{
//		return $this->database->create($moment);
//	}
//
//	function update_moment($moment)
//	{
//		$condition = new EqualityCondition(Moment :: PROPERTY_ID, $moment->get_id());
//		return $this->database->update($moment, $condition);
//	}
//
//	function delete_moment($moment)
//	{
//		$condition = new EqualityCondition(Moment :: PROPERTY_ID, $moment->get_id());
//		return $this->database->delete($moment->get_table_name(), $condition);
//	}
//
//	function count_moments($condition = null)
//	{
//		return $this->database->count_objects(Moment :: get_table_name(), $condition);
//	}
//
//	function retrieve_moment($id)
//	{
//		$condition = new EqualityCondition(Moment :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(Moment :: get_table_name(), $condition);
//	}
//
//	function retrieve_moments($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(Moment :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_period_id()
//	{
//		return $this->database->get_next_id(Period :: get_table_name());
//	}
//
//	function create_period($period)
//	{
//		return $this->database->create($period);
//	}
//
//	function update_period($period)
//	{
//		$condition = new EqualityCondition(Period :: PROPERTY_ID, $period->get_id());
//		return $this->database->update($period, $condition);
//	}
//
//	function delete_period($period)
//	{
//		$condition = new EqualityCondition(Period :: PROPERTY_ID, $period->get_id());
//		return $this->database->delete($period->get_table_name(), $condition);
//	}
//
//	function count_periods($condition = null)
//	{
//		return $this->database->count_objects(Period :: get_table_name(), $condition);
//	}
//
//	function retrieve_period($id)
//	{
//		$condition = new EqualityCondition(Period :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(Period :: get_table_name(), $condition);
//	}
//
//	function retrieve_periods($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(Period :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//
//	function get_next_place_id()
//	{
//		return $this->database->get_next_id(Place :: get_table_name());
//	}
//
//	function create_place($place)
//	{
//		return $this->database->create($place);
//	}
//
//	function update_place($place)
//	{
//		$condition = new EqualityCondition(Place :: PROPERTY_ID, $place->get_id());
//		return $this->database->update($place, $condition);
//	}
//
//	function delete_place($place)
//	{
//		$condition = new EqualityCondition(Place :: PROPERTY_ID, $place->get_id());
//		return $this->database->delete($place->get_table_name(), $condition);
//	}
//
//	function count_places($condition = null)
//	{
//		return $this->database->count_objects(Place :: get_table_name(), $condition);
//	}
//
//	function retrieve_place($id)
//	{
//		$condition = new EqualityCondition(Place :: PROPERTY_ID, $id);
//		return $this->database->retrieve_object(Place :: get_table_name(), $condition);
//	}
//
//	function retrieve_places($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->database->retrieve_objects(Place :: get_table_name(), $condition, $offset, $max_objects, $order_by);
//	}
//	
//	function retrieve_category_root($id)
// 	{
// 		$conditions = array();
// 		$conditions[] = new EqualityCondition(CourseInternshipPlannerCategory :: PROPERTY_PARENT_ID, 0);
// 		$condition = new AndCondition($conditions);
// 		return $this->retrieve_categories($condition)->next_result();
// 	}


}
?>