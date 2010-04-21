<?php

require_once dirname ( __FILE__ ) . '/../../mentor.class.php';

class DefaultInternshipOrganizerMentorTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerMentorTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipOrganizerMentor::PROPERTY_TITLE, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerMentor::PROPERTY_FIRSTNAME, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerMentor::PROPERTY_LASTNAME, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerMentor::PROPERTY_EMAIL, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerMentor::PROPERTY_TELEPHONE, true );
//		$columns [] = new ObjectTableColumn ( InternshipOrganizerMentor::PROPERTY_USER_ID, true );
		
		return $columns;
	}
}
?>