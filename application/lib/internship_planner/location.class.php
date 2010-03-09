<?php 
/**
 * internship planner
 */

/**
 * This class describes a Location data object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Location extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Location properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_STREET = 'street';
	const PROPERTY_STREET_NUMBER = 'street_number';
	const PROPERTY_PLACE_ID = 'place_id';
	const PROPERTY_LOCATION_GROUP_ID = 'location_group_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_STREET, self :: PROPERTY_STREET_NUMBER, self :: PROPERTY_PLACE_ID, self :: PROPERTY_LOCATION_GROUP_ID);
	}

	function get_data_manager()
	{
		return Internship plannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this Location.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Location.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this Location.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Location.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the street of this Location.
	 * @return the street.
	 */
	function get_street()
	{
		return $this->get_default_property(self :: PROPERTY_STREET);
	}

	/**
	 * Sets the street of this Location.
	 * @param street
	 */
	function set_street($street)
	{
		$this->set_default_property(self :: PROPERTY_STREET, $street);
	}

	/**
	 * Returns the street_number of this Location.
	 * @return the street_number.
	 */
	function get_street_number()
	{
		return $this->get_default_property(self :: PROPERTY_STREET_NUMBER);
	}

	/**
	 * Sets the street_number of this Location.
	 * @param street_number
	 */
	function set_street_number($street_number)
	{
		$this->set_default_property(self :: PROPERTY_STREET_NUMBER, $street_number);
	}

	/**
	 * Returns the place_id of this Location.
	 * @return the place_id.
	 */
	function get_place_id()
	{
		return $this->get_default_property(self :: PROPERTY_PLACE_ID);
	}

	/**
	 * Sets the place_id of this Location.
	 * @param place_id
	 */
	function set_place_id($place_id)
	{
		$this->set_default_property(self :: PROPERTY_PLACE_ID, $place_id);
	}

	/**
	 * Returns the location_group_id of this Location.
	 * @return the location_group_id.
	 */
	function get_location_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOCATION_GROUP_ID);
	}

	/**
	 * Sets the location_group_id of this Location.
	 * @param location_group_id
	 */
	function set_location_group_id($location_group_id)
	{
		$this->set_default_property(self :: PROPERTY_LOCATION_GROUP_ID, $location_group_id);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>