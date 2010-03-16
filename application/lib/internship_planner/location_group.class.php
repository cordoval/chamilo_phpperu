<?php 
/**
 * internship_planner
 */

/**
 * This class describes a InternshipLocationGroup data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipLocationGroup extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipLocationGroup properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this InternshipLocationGroup.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this InternshipLocationGroup.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this InternshipLocationGroup.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this InternshipLocationGroup.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>