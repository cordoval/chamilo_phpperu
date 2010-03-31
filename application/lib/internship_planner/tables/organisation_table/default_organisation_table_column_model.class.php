<?php

require_once dirname ( __FILE__ ) . '/../../organisation.class.php';

class DefaultInternshipPlannerOrganisationTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipPlannerOrganisationTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipPlannerOrganisation::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipPlannerOrganisation::PROPERTY_DESCRIPTION, true );
		
		return $columns;
	}
}
?>