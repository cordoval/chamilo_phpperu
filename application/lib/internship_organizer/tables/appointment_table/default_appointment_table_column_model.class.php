<?php

require_once dirname ( __FILE__ ) . '/../../appointment.class.php';

class DefaultInternshipOrganizerAppointmentTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerAppointmentTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
	
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAppointment::PROPERTY_TITLE, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAppointment::PROPERTY_DESCRIPTION, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAppointment::PROPERTY_TYPE, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAppointment::PROPERTY_STATUS, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAppointment::PROPERTY_CREATED, true );
		return $columns;
	}
}
?>