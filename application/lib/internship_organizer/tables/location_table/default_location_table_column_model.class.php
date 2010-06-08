<?php

/** @author Steven Willaert */

require_once dirname ( __FILE__ ) . '/../../location.class.php';

class DefaultInternshipOrganizerLocationTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerLocationTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_ADDRESS, true );
		$region_column = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_REGION_ID, true );
		$region_column->set_title(Translation :: get('City'));
		$columns [] = $region_column;
		$columns [] = new ObjectTableColumn ( InternshipOrganizerLocation::PROPERTY_DESCRIPTION, true );
		
		return $columns;
	}
}
?>