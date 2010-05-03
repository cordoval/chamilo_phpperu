<?php

class DefaultSurveyPageTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function DefaultSurveyPageTableColumnModel() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( SurveyPage::PROPERTY_NAME, true );
		$columns [] = new ObjectTableColumn ( SurveyPage::PROPERTY_DESCRIPTION, true );
		return $columns;
	}
}
?>