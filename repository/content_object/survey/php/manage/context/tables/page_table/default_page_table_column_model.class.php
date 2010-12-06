<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\ObjectTableColumn;
use common\libraries\ObjectTableColumnModel;
use repository\content_object\survey_page\SurveyPage;


class DefaultSurveyPageTableColumnModel extends ObjectTableColumnModel {
	
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct ( self::get_default_columns (), 0 );
	}
	
	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns() {
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( SurveyPage::PROPERTY_TITLE, true );
		$columns [] = new ObjectTableColumn ( SurveyPage::PROPERTY_DESCRIPTION, true );
		return $columns;
	}
}
?>