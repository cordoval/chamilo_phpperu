<?php

class InternshipOrganizerLocation extends DataClass {
	const CLASS_NAME = __CLASS__;
	
	/**
	 * InternshipOrganizerLocation properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ORGANISATION_ID = 'organisation_id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_STREET = 'street';
	const PROPERTY_STREET_NUMBER = 'street_number';
	const PROPERTY_CITY = 'city';
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names() {
		return array (self::PROPERTY_ID, self::PROPERTY_ORGANISATION_ID, self::PROPERTY_NAME, self::PROPERTY_STREET, self::PROPERTY_STREET_NUMBER, self::PROPERTY_CITY );
	}
	
	function get_data_manager() {
		return InternshipOrganizerDataManager::get_instance ();
	}
	
	/**
	 * Returns the id of this InternshipOrganizerLocation.
	 * @return the id.
	 */
	function get_id() {
		return $this->get_default_property ( self::PROPERTY_ID );
	}
	
	/**
	 * Sets the id of this InternshipOrganizerLocation.
	 * @param id
	 */
	function set_id($id) {
		$this->set_default_property ( self::PROPERTY_ID, $id );
	}
	
	/**
	 * Returns the id of this InternshipOrganizerOrganisation.
	 * @return the id.
	 */
	function get_organisation_id() {
		return $this->get_default_property ( self::PROPERTY_ORGANISATION_ID );
	}
	
	/**
	 * Sets the id of this InternshipOrganizerOrganisation.
	 * @param id
	 */
	function set_organisation_id($id) {
		$this->set_default_property ( self::PROPERTY_ORGANISATION_ID, $id );
	}
	
	/**
	 * Returns the name of this InternshipOrganizerLocation.
	 * @return the name.
	 */
	function get_name() {
		return $this->get_default_property ( self::PROPERTY_NAME );
	}
	
	/**
	 * Sets the name of this InternshipOrganizerLocation.
	 * @param name
	 */
	function set_name($name) {
		$this->set_default_property ( self::PROPERTY_NAME, $name );
	}
	
	/**
	 * Returns the street of this InternshipOrganizerLocation.
	 * @return the street.
	 */
	function get_street() {
		return $this->get_default_property ( self::PROPERTY_STREET );
	}
	
	/**
	 * Sets the street of this InternshipOrganizerLocation.
	 * @param street
	 */
	function set_street($street) {
		$this->set_default_property ( self::PROPERTY_STREET, $street );
	}
	
	/**
	 * Returns the street_number of this InternshipOrganizerLocation.
	 * @return the street_number.
	 */
	function get_street_number() {
		return $this->get_default_property ( self::PROPERTY_STREET_NUMBER );
	}
	
	/**
	 * Sets the street_number of this InternshipOrganizerLocation.
	 * @param street_number
	 */
	function set_street_number($street_number) {
		$this->set_default_property ( self::PROPERTY_STREET_NUMBER, $street_number );
	}
	
	/**
	 * Returns the city of this InternshipOrganizerLocation.
	 * @return the city.
	 */
	function get_city() {
		return $this->get_default_property ( self::PROPERTY_CITY );
	}
	
	/**
	 * Sets the city of this InternshipOrganizerLocation.
	 * @param city
	 */
	function set_city($city) {
		$this->set_default_property ( self::PROPERTY_CITY, $city );
	}
	
	function get_organisation(){
		return $this->get_data_manager()->retrieve_organisation($this->get_organisation_id());
	}
	
	static function get_table_name() {
		return 'location';
//		return Utilities::camelcase_to_underscores ( self::CLASS_NAME );
	
	}
}

?>