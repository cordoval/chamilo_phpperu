<?php
/**
 * @package cda.tables.cda_language_table
 */
require_once dirname(__FILE__).'/../../cda_language.class.php';

/**
 * Default column model for the cda_language table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultCdaLanguageTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCdaLanguageTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();

		$columns[] = new ObjectTableColumn(CdaLanguage :: PROPERTY_ENGLISH_NAME);
		$columns[] = new ObjectTableColumn('TranslationProgress', false);
		return $columns;
	}
}
?>