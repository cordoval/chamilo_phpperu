<?php 
/**
 * internship_planner
 */

/**
 * This class describes a Place data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class Place extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Place properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_LEFT_VALUE = 'left_value';
	const PROPERTY_RIGHT_VALUE = 'right_value';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_PARENT_ID, self :: PROPERTY_LEFT_VALUE, self :: PROPERTY_RIGHT_VALUE);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this Place.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Place.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this Place.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Place.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the parent_id of this Place.
	 * @return the parent_id.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}

	/**
	 * Sets the parent_id of this Place.
	 * @param parent_id
	 */
	function set_parent_id($parent_id)
	{
		$this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
	}

	/**
	 * Returns the left_value of this Place.
	 * @return the left_value.
	 */
	function get_left_value()
	{
		return $this->get_default_property(self :: PROPERTY_LEFT_VALUE);
	}

	/**
	 * Sets the left_value of this Place.
	 * @param left_value
	 */
	function set_left_value($left_value)
	{
		$this->set_default_property(self :: PROPERTY_LEFT_VALUE, $left_value);
	}

	/**
	 * Returns the right_value of this Place.
	 * @return the right_value.
	 */
	function get_right_value()
	{
		return $this->get_default_property(self :: PROPERTY_RIGHT_VALUE);
	}

	/**
	 * Sets the right_value of this Place.
	 * @param right_value
	 */
	function set_right_value($right_value)
	{
		$this->set_default_property(self :: PROPERTY_RIGHT_VALUE, $right_value);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>