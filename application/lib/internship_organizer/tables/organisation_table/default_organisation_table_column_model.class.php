<?php

/** @author Steven Willaert */

require_once dirname ( __FILE__ ) . '/../../organisation.class.php';

class DefaultInternshipOrganizerOrganisationTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerOrganisationTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_NAME, true );
//		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_ADDRESS, true );
//		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_POSTCODE, true );
//		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_CITY, true );
//		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_TELEPHONE, true );
//		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_FAX, true );
//		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_EMAIL, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerOrganisation::PROPERTY_DESCRIPTION, true );
		
		return $columns;
	}
}
?>