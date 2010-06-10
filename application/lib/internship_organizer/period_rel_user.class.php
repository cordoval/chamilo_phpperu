<?php 
/**
 * This class describes a InternshipPeriodRelUser data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerPeriodRelUser extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipOrganizerPeriodRelUser properties
	 */
	const PROPERTY_PERIOD_ID = 'period_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_USER_TYPE = 'user_type';
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PERIOD_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_USER_TYPE);
	}

	function get_data_manager()
	{
		return InternshipOrganizerDataManager :: get_instance();
	}

	/**
	 * Returns the period_id of this InternshipOrganizerPeriodRelUser.
	 * @return the period_id.
	 */
	function get_period_id()
	{
		return $this->get_default_property(self :: PROPERTY_PERIOD_ID);
	}

	/**
	 * Sets the period_id of this InternshipOrganizerPeriodRelUser.
	 * @param period_id
	 */
	function set_period_id($period_id)
	{
		$this->set_default_property(self :: PROPERTY_PERIOD_ID, $period_id);
	}

	/**
	 * Returns the user_id of this InternshipOrganizerPeriodRelUser.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this InternshipOrganizerPeriodRelUser.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
/**
	 * Returns the user_type of this InternshipOrganizerPeriodRelUser.
	 * @return the user_type.
	 */
	function get_user_type()
	{
		return $this->get_default_property(self :: PROPERTY_USER_TYPE);
	}

	/**
	 * Sets the user_type of this InternshipOrganizerPeriodRelUser.
	 * @param user_type
	 */
	function set_user_type($user_type)
	{
		$this->set_default_property(self :: PROPERTY_USER_TYPE, $user_type);
	}
	

	static function get_table_name()
	{
		return 'period_rel_user';
//		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>