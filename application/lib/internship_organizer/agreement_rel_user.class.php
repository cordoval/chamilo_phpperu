<?php
/**
 * This class describes a InternshipAgreementRelUser data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerAgreementRelUser extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipAgreementRelUser properties
	 */
	const PROPERTY_AGREEMENT_ID = 'agreement_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_USER_TYPE = 'user_type';
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_AGREEMENT_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_USER_TYPE);
	}

	function get_data_manager()
	{
		return InternshipOrganizerDataManager :: get_instance();
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

/**
	 * Returns the user_type of this InternshipAgreementRelUser.
	 * @return the user_type.
	 */
	function get_user_type()
	{
		return $this->get_default_property(self :: PROPERTY_USER_TYPE);
	}

	/**
	 * Sets the user_type of this InternshipAgreementRelUser.
	 * @param user_type
	 */
	function set_user_type($user_type)
	{
		$this->set_default_property(self :: PROPERTY_USER_TYPE, $user_type);
	}
	
	
	static function get_table_name()
	{
		return 'agreement_rel_user';
//		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>