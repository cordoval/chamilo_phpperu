<?php
/**
 * @package internship planner.datamanager
 */
require_once dirname(__FILE__).'/../category.class.php';
require_once dirname(__FILE__).'/../location.class.php';
require_once dirname(__FILE__).'/../location_group.class.php';
require_once dirname(__FILE__).'/../location_rel_category.class.php';
require_once dirname(__FILE__).'/../location_rel_mentor.class.php';
require_once dirname(__FILE__).'/../location_rel_moment.class.php';
require_once dirname(__FILE__).'/../location_rel_type.class.php';
require_once dirname(__FILE__).'/../mentor.class.php';
require_once dirname(__FILE__).'/../moment.class.php';
require_once dirname(__FILE__).'/../period.class.php';
require_once dirname(__FILE__).'/../place.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author ehb
 */

class DatabaseInternship plannerDataManager extends Internship plannerDataManager
{
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases[Category :: get_table_name()] = 'cary';
		$aliases[Location :: get_table_name()] = 'loon';
		$aliases[LocationGroup :: get_table_name()] = 'loup';
		$aliases[LocationRelCategory :: get_table_name()] = 'lory';
		$aliases[LocationRelMentor :: get_table_name()] = 'loor';
		$aliases[LocationRelMoment :: get_table_name()] = 'lont';
		$aliases[LocationRelType :: get_table_name()] = 'lope';
		$aliases[Mentor :: get_table_name()] = 'meor';
		$aliases[Moment :: get_table_name()] = 'mont';
		$aliases[Period :: get_table_name()] = 'peod';
		$aliases[Place :: get_table_name()] = 'plce';

		$this->database = new Database($aliases);
		$this->database->set_prefix('internship planner_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_category_id()
	{
		return $this->database->get_next_id(Category :: get_table_name());
	}

	function create_category($category)
	{
		return $this->database->create($category);
	}

	function update_category($category)
	{
		$condition = new EqualityCondition(Category :: PROPERTY_ID, $category->get_id());
		return $this->database->update($category, $condition);
	}

	function delete_category($category)
	{
		$condition = new EqualityCondition(Category :: PROPERTY_ID, $category->get_id());
		return $this->database->delete($category->get_table_name(), $condition);
	}

	function count_categories($condition = null)
	{
		return $this->database->count_objects(Category :: get_table_name(), $condition);
	}

	function retrieve_category($id)
	{
		$condition = new EqualityCondition(Category :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Category :: get_table_name(), $condition);
	}

	function retrieve_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Category :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_location_id()
	{
		return $this->database->get_next_id(Location :: get_table_name());
	}

	function create_location($location)
	{
		return $this->database->create($location);
	}

	function update_location($location)
	{
		$condition = new EqualityCondition(Location :: PROPERTY_ID, $location->get_id());
		return $this->database->update($location, $condition);
	}

	function delete_location($location)
	{
		$condition = new EqualityCondition(Location :: PROPERTY_ID, $location->get_id());
		return $this->database->delete($location->get_table_name(), $condition);
	}

	function count_locations($condition = null)
	{
		return $this->database->count_objects(Location :: get_table_name(), $condition);
	}

	function retrieve_location($id)
	{
		$condition = new EqualityCondition(Location :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Location :: get_table_name(), $condition);
	}

	function retrieve_locations($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Location :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_location_group_id()
	{
		return $this->database->get_next_id(LocationGroup :: get_table_name());
	}

	function create_location_group($location_group)
	{
		return $this->database->create($location_group);
	}

	function update_location_group($location_group)
	{
		$condition = new EqualityCondition(LocationGroup :: PROPERTY_ID, $location_group->get_id());
		return $this->database->update($location_group, $condition);
	}

	function delete_location_group($location_group)
	{
		$condition = new EqualityCondition(LocationGroup :: PROPERTY_ID, $location_group->get_id());
		return $this->database->delete($location_group->get_table_name(), $condition);
	}

	function count_location_groups($condition = null)
	{
		return $this->database->count_objects(LocationGroup :: get_table_name(), $condition);
	}

	function retrieve_location_group($id)
	{
		$condition = new EqualityCondition(LocationGroup :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(LocationGroup :: get_table_name(), $condition);
	}

	function retrieve_location_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(LocationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_location_rel_category_id()
	{
		return $this->database->get_next_id(LocationRelCategory :: get_table_name());
	}

	function create_location_rel_category($location_rel_category)
	{
		return $this->database->create($location_rel_category);
	}

	function update_location_rel_category($location_rel_category)
	{
		$condition = new EqualityCondition(LocationRelCategory :: PROPERTY_ID, $location_rel_category->get_id());
		return $this->database->update($location_rel_category, $condition);
	}

	function delete_location_rel_category($location_rel_category)
	{
		$condition = new EqualityCondition(LocationRelCategory :: PROPERTY_ID, $location_rel_category->get_id());
		return $this->database->delete($location_rel_category->get_table_name(), $condition);
	}

	function count_location_rel_categories($condition = null)
	{
		return $this->database->count_objects(LocationRelCategory :: get_table_name(), $condition);
	}

	function retrieve_location_rel_category($id)
	{
		$condition = new EqualityCondition(LocationRelCategory :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(LocationRelCategory :: get_table_name(), $condition);
	}

	function retrieve_location_rel_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(LocationRelCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_location_rel_mentor_id()
	{
		return $this->database->get_next_id(LocationRelMentor :: get_table_name());
	}

	function create_location_rel_mentor($location_rel_mentor)
	{
		return $this->database->create($location_rel_mentor);
	}

	function update_location_rel_mentor($location_rel_mentor)
	{
		$condition = new EqualityCondition(LocationRelMentor :: PROPERTY_ID, $location_rel_mentor->get_id());
		return $this->database->update($location_rel_mentor, $condition);
	}

	function delete_location_rel_mentor($location_rel_mentor)
	{
		$condition = new EqualityCondition(LocationRelMentor :: PROPERTY_ID, $location_rel_mentor->get_id());
		return $this->database->delete($location_rel_mentor->get_table_name(), $condition);
	}

	function count_location_rel_mentors($condition = null)
	{
		return $this->database->count_objects(LocationRelMentor :: get_table_name(), $condition);
	}

	function retrieve_location_rel_mentor($id)
	{
		$condition = new EqualityCondition(LocationRelMentor :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(LocationRelMentor :: get_table_name(), $condition);
	}

	function retrieve_location_rel_mentors($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(LocationRelMentor :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_location_rel_moment_id()
	{
		return $this->database->get_next_id(LocationRelMoment :: get_table_name());
	}

	function create_location_rel_moment($location_rel_moment)
	{
		return $this->database->create($location_rel_moment);
	}

	function update_location_rel_moment($location_rel_moment)
	{
		$condition = new EqualityCondition(LocationRelMoment :: PROPERTY_ID, $location_rel_moment->get_id());
		return $this->database->update($location_rel_moment, $condition);
	}

	function delete_location_rel_moment($location_rel_moment)
	{
		$condition = new EqualityCondition(LocationRelMoment :: PROPERTY_ID, $location_rel_moment->get_id());
		return $this->database->delete($location_rel_moment->get_table_name(), $condition);
	}

	function count_location_rel_moments($condition = null)
	{
		return $this->database->count_objects(LocationRelMoment :: get_table_name(), $condition);
	}

	function retrieve_location_rel_moment($id)
	{
		$condition = new EqualityCondition(LocationRelMoment :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(LocationRelMoment :: get_table_name(), $condition);
	}

	function retrieve_location_rel_moments($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(LocationRelMoment :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_location_rel_type_id()
	{
		return $this->database->get_next_id(LocationRelType :: get_table_name());
	}

	function create_location_rel_type($location_rel_type)
	{
		return $this->database->create($location_rel_type);
	}

	function update_location_rel_type($location_rel_type)
	{
		$condition = new EqualityCondition(LocationRelType :: PROPERTY_ID, $location_rel_type->get_id());
		return $this->database->update($location_rel_type, $condition);
	}

	function delete_location_rel_type($location_rel_type)
	{
		$condition = new EqualityCondition(LocationRelType :: PROPERTY_ID, $location_rel_type->get_id());
		return $this->database->delete($location_rel_type->get_table_name(), $condition);
	}

	function count_location_rel_types($condition = null)
	{
		return $this->database->count_objects(LocationRelType :: get_table_name(), $condition);
	}

	function retrieve_location_rel_type($id)
	{
		$condition = new EqualityCondition(LocationRelType :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(LocationRelType :: get_table_name(), $condition);
	}

	function retrieve_location_rel_types($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(LocationRelType :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_mentor_id()
	{
		return $this->database->get_next_id(Mentor :: get_table_name());
	}

	function create_mentor($mentor)
	{
		return $this->database->create($mentor);
	}

	function update_mentor($mentor)
	{
		$condition = new EqualityCondition(Mentor :: PROPERTY_ID, $mentor->get_id());
		return $this->database->update($mentor, $condition);
	}

	function delete_mentor($mentor)
	{
		$condition = new EqualityCondition(Mentor :: PROPERTY_ID, $mentor->get_id());
		return $this->database->delete($mentor->get_table_name(), $condition);
	}

	function count_mentors($condition = null)
	{
		return $this->database->count_objects(Mentor :: get_table_name(), $condition);
	}

	function retrieve_mentor($id)
	{
		$condition = new EqualityCondition(Mentor :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Mentor :: get_table_name(), $condition);
	}

	function retrieve_mentors($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Mentor :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_moment_id()
	{
		return $this->database->get_next_id(Moment :: get_table_name());
	}

	function create_moment($moment)
	{
		return $this->database->create($moment);
	}

	function update_moment($moment)
	{
		$condition = new EqualityCondition(Moment :: PROPERTY_ID, $moment->get_id());
		return $this->database->update($moment, $condition);
	}

	function delete_moment($moment)
	{
		$condition = new EqualityCondition(Moment :: PROPERTY_ID, $moment->get_id());
		return $this->database->delete($moment->get_table_name(), $condition);
	}

	function count_moments($condition = null)
	{
		return $this->database->count_objects(Moment :: get_table_name(), $condition);
	}

	function retrieve_moment($id)
	{
		$condition = new EqualityCondition(Moment :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Moment :: get_table_name(), $condition);
	}

	function retrieve_moments($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Moment :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_period_id()
	{
		return $this->database->get_next_id(Period :: get_table_name());
	}

	function create_period($period)
	{
		return $this->database->create($period);
	}

	function update_period($period)
	{
		$condition = new EqualityCondition(Period :: PROPERTY_ID, $period->get_id());
		return $this->database->update($period, $condition);
	}

	function delete_period($period)
	{
		$condition = new EqualityCondition(Period :: PROPERTY_ID, $period->get_id());
		return $this->database->delete($period->get_table_name(), $condition);
	}

	function count_periods($condition = null)
	{
		return $this->database->count_objects(Period :: get_table_name(), $condition);
	}

	function retrieve_period($id)
	{
		$condition = new EqualityCondition(Period :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Period :: get_table_name(), $condition);
	}

	function retrieve_periods($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Period :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_place_id()
	{
		return $this->database->get_next_id(Place :: get_table_name());
	}

	function create_place($place)
	{
		return $this->database->create($place);
	}

	function update_place($place)
	{
		$condition = new EqualityCondition(Place :: PROPERTY_ID, $place->get_id());
		return $this->database->update($place, $condition);
	}

	function delete_place($place)
	{
		$condition = new EqualityCondition(Place :: PROPERTY_ID, $place->get_id());
		return $this->database->delete($place->get_table_name(), $condition);
	}

	function count_places($condition = null)
	{
		return $this->database->count_objects(Place :: get_table_name(), $condition);
	}

	function retrieve_place($id)
	{
		$condition = new EqualityCondition(Place :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Place :: get_table_name(), $condition);
	}

	function retrieve_places($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Place :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

}
?>