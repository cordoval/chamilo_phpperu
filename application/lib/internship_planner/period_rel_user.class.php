<?php 
/**
 * This class describes a InternshipPeriodRelUser data object
 * @author Sven Vanhoecke
 */
class InternshipPlannerPeriodRelUser extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipPlannerPeriodRelUser properties
	 */
	const PROPERTY_PERIOD_ID = 'period_id';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PERIOD_ID, self :: PROPERTY_USER_ID);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the period_id of this InternshipPlannerPeriodRelUser.
	 * @return the period_id.
	 */
	function get_period_id()
	{
		return $this->get_default_property(self :: PROPERTY_PERIOD_ID);
	}

	/**
	 * Sets the period_id of this InternshipPlannerPeriodRelUser.
	 * @param period_id
	 */
	function set_period_id($period_id)
	{
		$this->set_default_property(self :: PROPERTY_PERIOD_ID, $period_id);
	}

	/**
	 * Returns the user_id of this InternshipPlannerPeriodRelUser.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this InternshipPlannerPeriodRelUser.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>