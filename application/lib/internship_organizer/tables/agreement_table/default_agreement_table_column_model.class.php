<?php

require_once dirname ( __FILE__ ) . '/../../agreement.class.php';

class DefaultInternshipOrganizerAgreementTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerAgreementTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_DESCRIPTION, true );
		
		return $columns;
	}
}
?>