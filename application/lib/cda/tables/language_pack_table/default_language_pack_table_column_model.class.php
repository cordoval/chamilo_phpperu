<?php
/**
 * @package cda.tables.language_pack_table
 */
require_once dirname(__FILE__).'/../../language_pack.class.php';

/**
 * Default column model for the language_pack table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultLanguagePackTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultLanguagePackTableColumnModel()
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

		$columns[] = new ObjectTableColumn(LanguagePack :: PROPERTY_BRANCH);
		$columns[] = new ObjectTableColumn(LanguagePack :: PROPERTY_NAME);
		$columns[] = new ObjectTableColumn(LanguagePack :: PROPERTY_TYPE);
		$columns[] = new StaticTableColumn(Translation :: get('TranslationProgress'));

		return $columns;
	}
}
?>