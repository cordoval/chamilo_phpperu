<?php 
/**
 * internship_planner
 */

/**
 * This class describes a Moment data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class Moment extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Moment properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_BEGIN = 'begin';
	const PROPERTY_END = 'end';
	const PROPERTY_CATEGORY_ID = 'category_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_BEGIN, self :: PROPERTY_END, self :: PROPERTY_CATEGORY_ID);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this Moment.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Moment.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the user_id of this Moment.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Moment.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}

	/**
	 * Returns the begin of this Moment.
	 * @return the begin.
	 */
	function get_begin()
	{
		return $this->get_default_property(self :: PROPERTY_BEGIN);
	}

	/**
	 * Sets the begin of this Moment.
	 * @param begin
	 */
	function set_begin($begin)
	{
		$this->set_default_property(self :: PROPERTY_BEGIN, $begin);
	}

	/**
	 * Returns the end of this Moment.
	 * @return the end.
	 */
	function get_end()
	{
		return $this->get_default_property(self :: PROPERTY_END);
	}

	/**
	 * Sets the end of this Moment.
	 * @param end
	 */
	function set_end($end)
	{
		$this->set_default_property(self :: PROPERTY_END, $end);
	}

	/**
	 * Returns the category_id of this Moment.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}

	/**
	 * Sets the category_id of this Moment.
	 * @param category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>