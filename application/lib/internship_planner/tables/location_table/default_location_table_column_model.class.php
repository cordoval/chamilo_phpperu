<?php

require_once dirname ( __FILE__ ) . '/../../location.class.php';

class DefaultInternshipLocationTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipLocationTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipLocation::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipLocation::PROPERTY_CITY, true );
		$columns [] = new ObjectTableColumn ( InternshipLocation::PROPERTY_STREET, true );
		return $columns;
	}
}
?>