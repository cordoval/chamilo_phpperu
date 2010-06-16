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
		$columns [] = new ObjectTableColumn ( User::PROPERTY_FIRSTNAME, false );
		$columns [] = new ObjectTableColumn ( User::PROPERTY_LASTNAME, false );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_DESCRIPTION, true );
		$columns [] = new ObjectTableColumn ( Translation :: get('InternshipOrganizerPeriodName'), false );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_BEGIN, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_END, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerAgreement::PROPERTY_STATUS, true );
		
		return $columns;
	}
}
?>