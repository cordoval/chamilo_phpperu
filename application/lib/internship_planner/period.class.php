<?php 
/**
 * internship_planner
 */

/**
 * This class describes a Period data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class Period extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Period properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_BEGIN = 'begin';
	const PROPERTY_END = 'end';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_BEGIN, self :: PROPERTY_END);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this Period.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Period.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this Period.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Period.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the begin of this Period.
	 * @return the begin.
	 */
	function get_begin()
	{
		return $this->get_default_property(self :: PROPERTY_BEGIN);
	}

	/**
	 * Sets the begin of this Period.
	 * @param begin
	 */
	function set_begin($begin)
	{
		$this->set_default_property(self :: PROPERTY_BEGIN, $begin);
	}

	/**
	 * Returns the end of this Period.
	 * @return the end.
	 */
	function get_end()
	{
		return $this->get_default_property(self :: PROPERTY_END);
	}

	/**
	 * Sets the end of this Period.
	 * @param end
	 */
	function set_end($end)
	{
		$this->set_default_property(self :: PROPERTY_END, $end);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>