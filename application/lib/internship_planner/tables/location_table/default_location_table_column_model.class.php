<?php

require_once dirname ( __FILE__ ) . '/../../location.class.php';

class DefaultInternshipPlannerLocationTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipPlannerLocationTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipPlannerLocation::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipPlannerLocation::PROPERTY_CITY, true );
		$columns [] = new ObjectTableColumn ( InternshipPlannerLocation::PROPERTY_STREET, true );
		return $columns;
	}
}
?>