<?php 
/**
 * internship planner
 */

/**
 * This class describes a LocationRelType data object
 * @author Sven Vanpoucke
 * @author ehb
 */
class LocationRelType extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * LocationRelType properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LOCATION_ID = 'location_id';
	const PROPERTY_TYPE_ID = 'type_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_LOCATION_ID, self :: PROPERTY_TYPE_ID);
	}

	function get_data_manager()
	{
		return Internship plannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this LocationRelType.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this LocationRelType.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the location_id of this LocationRelType.
	 * @return the location_id.
	 */
	function get_location_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
	}

	/**
	 * Sets the location_id of this LocationRelType.
	 * @param location_id
	 */
	function set_location_id($location_id)
	{
		$this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
	}

	/**
	 * Returns the type_id of this LocationRelType.
	 * @return the type_id.
	 */
	function get_type_id()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE_ID);
	}

	/**
	 * Sets the type_id of this LocationRelType.
	 * @param type_id
	 */
	function set_type_id($type_id)
	{
		$this->set_default_property(self :: PROPERTY_TYPE_ID, $type_id);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>