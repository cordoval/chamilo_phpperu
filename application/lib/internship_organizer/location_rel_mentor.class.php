<?php 
/**
 * internship_organizer
 * OPGEKUIST
 */

/**
 * This class describes a InternshipOrganizerLocationRelMentor data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipOrganizerLocationRelMentor extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * InternshipOrganizerLocationRelMentor properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LOCATION_ID = 'location_id';
	const PROPERTY_MENTOR_ID = 'mentor_id';
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, 
		self :: PROPERTY_LOCATION_ID, 
		self :: PROPERTY_MENTOR_ID);
	}

	function get_data_manager()
	{
		return InternshipOrganizerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this InternshipOrganizerLocationRelMentor.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this InternshipOrganizerLocationRelMentor.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	
	/**
	 * Returns the location_id of this InternshipOrganizerLocationRelMentor.
	 * @return the location_id.
	 */
	function get_location_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
	}

	/**
	 * Sets the location_id of this InternshipOrganizerLocationRelMentor.
	 * @param location_id
	 */
	function set_location_id($location_id)
	{
		$this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
	}

	/**
	 * Returns the mentor_id of this InternshipOrganizerLocationRelMentor.
	 * @return the mentor_id.
	 */
	function get_mentor_id()
	{
		return $this->get_default_property(self :: PROPERTY_MENTOR_ID);
	}

	/**
	 * Sets the mentor_id of this InternshipOrganizerLocationRelMentor.
	 * @param mentor_id
	 */
	function set_mentor_id($mentor_id)
	{
		$this->set_default_property(self :: PROPERTY_MENTOR_ID, $mentor_id);
	}


	static function get_table_name()
	{
		return 'location_rel_mentor';
//		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>