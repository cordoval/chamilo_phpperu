<?php

require_once dirname ( __FILE__ ) . '/../../moment.class.php';

class DefaultInternshipPlannerMomentTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultInternshipPlannerMomentTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipPlannerMoment::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( InternshipPlannerMoment::PROPERTY_DESCRIPTION, true );
		$columns [] = new ObjectTableColumn ( InternshipPlannerMoment::PROPERTY_BEGIN, true );
		$columns [] = new ObjectTableColumn ( InternshipPlannerMoment::PROPERTY_END, true );
		return $columns;
	}
}
?>