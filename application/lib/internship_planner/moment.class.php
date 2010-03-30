<?php 

/**
 * This class describes a InternshipMoment data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerMoment extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Moment properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_BEGIN = 'begin';
	const PROPERTY_END = 'end';
	const PROPERTY_AGREEMENT_ID = 'agreement_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (	self :: PROPERTY_ID, 
						self :: PROPERTY_NAME, 
						self :: PROPERTY_DESCRIPTION,
						self :: PROPERTY_BEGIN, 
						self :: PROPERTY_END, 
						self :: PROPERTY_AGREEMENT_ID);
	}

	function get_data_manager()
	{
		return InternshipPlannerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this InternshipMoment.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this InternshipMoment.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this InternshipMoment.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this InternshipMoment.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the description of this InternshipMoment.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this InternshipMoment.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	
	/**
	 * Returns the begin of this InternshipMoment.
	 * @return the begin.
	 */
	function get_begin()
	{
		return $this->get_default_property(self :: PROPERTY_BEGIN);
	}

	/**
	 * Sets the begin of this InternshipMoment.
	 * @param begin
	 */
	function set_begin($begin)
	{
		$this->set_default_property(self :: PROPERTY_BEGIN, $begin);
	}

	/**
	 * Returns the end of this InternshipMoment.
	 * @return the end.
	 */
	function get_end()
	{
		return $this->get_default_property(self :: PROPERTY_END);
	}

	/**
	 * Sets the end of this InternshipMoment.
	 * @param end
	 */
	function set_end($end)
	{
		$this->set_default_property(self :: PROPERTY_END, $end);
	}

	/**
	 * Returns the agreement_id of this InternshipMoment.
	 * @return the agreement_id.
	 */
	function get_agreement_id()
	{
		return $this->get_default_property(self :: PROPERTY_AGREEMENT_ID);
	}

	/**
	 * Sets the agreement_id of this InternshipMoment.
	 * @param agreement_id
	 */
	function set_agreement_id($agreement_id)
	{
		$this->set_default_property(self :: PROPERTY_AGREEMENT_ID, $agreement_id);
	}


	static function get_table_name()
	{
		return 'moment';
//		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>