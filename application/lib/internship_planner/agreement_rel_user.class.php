<?php
/**
 * This class describes a InternshipAgreementRelUser data object
 * @author Sven Vanhoecke
 */
class InternshipPlannerAgreementRelUser extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipAgreementRelUser properties
	 */
	const PROPERTY_AGREEMENT_ID = 'agreement_id';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_AGREEMENT_ID, self :: PROPERTY_USER_ID);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the agreement_id of this InternshipAgreementRelUser.
	 * @return the agreement_id.
	 */
	function get_agreement_id()
	{
		return $this->get_default_property(self :: PROPERTY_AGREEMENT_ID);
	}

	/**
	 * Sets the agreement_id of this InternshipAgreementRelUser.
	 * @param agreement_id
	 */
	function set_agreement_id($agreement_id)
	{
		$this->set_default_property(self :: PROPERTY_AGREEMENT_ID, $agreement_id);
	}

	/**
	 * Returns the user_id of this InternshipAgreementRelUser.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this InternshipAgreementRelUser.
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