<?php
/**
 * This class describes a InternshipAgreementRelGroup data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerAgreementRelGroup extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipAgreementRelGroup properties
	 */
	const PROPERTY_AGREEMENT_ID = 'agreement_id';
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_USER_TYPE = 'user_type';
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_AGREEMENT_ID, self :: PROPERTY_GROUP_ID, self :: PROPERTY_USER_TYPE);
	}

	function get_data_manager()
	{
		return InternshipOrganizerDataManager :: get_instance();
	}

	/**
	 * Returns the agreement_id of this InternshipAgreementRelGroup.
	 * @return the agreement_id.
	 */
	function get_agreement_id()
	{
		return $this->get_default_property(self :: PROPERTY_AGREEMENT_ID);
	}

	/**
	 * Sets the agreement_id of this InternshipAgreementRelGroup.
	 * @param agreement_id
	 */
	function set_agreement_id($agreement_id)
	{
		$this->set_default_property(self :: PROPERTY_AGREEMENT_ID, $agreement_id);
	}

	/**
	 * Returns the group_id of this InternshipAgreementRelGroup.
	 * @return the group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Sets the group_id of this InternshipAgreementRelGroup.
	 * @param group_id
	 */
	function set_group_id($group_id)
	{
		$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
	}

/**
	 * Returns the user_type of this InternshipAgreementRelGroup.
	 * @return the user_type.
	 */
	function get_user_type()
	{
		return $this->get_default_property(self :: PROPERTY_USER_TYPE);
	}

	/**
	 * Sets the user_type of this InternshipAgreementRelGroup.
	 * @param user_type
	 */
	function set_user_type($user_type)
	{
		$this->set_default_property(self :: PROPERTY_USER_TYPE, $user_type);
	}
	
	
	static function get_table_name()
	{
		return 'agreement_rel_group';
//		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>