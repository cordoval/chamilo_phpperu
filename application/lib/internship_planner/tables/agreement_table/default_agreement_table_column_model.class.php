<?php

require_once dirname ( __FILE__ ) . '/../../agreement.class.php';

class DefaultInternshipPlannerAgreementTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipPlannerAgreementTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipPlannerAgreement::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipPlannerAgreement::PROPERTY_DESCRIPTION, true );
		
		return $columns;
	}
}
?>