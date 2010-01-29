<?php
/**
 * @package cda.tables.variable_table
 */
require_once dirname(__FILE__).'/../../translator_application.class.php';

/**
 * Default column model for the variable table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultTranslatorApplicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultTranslatorApplicationTableColumnModel()
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

		$alias = CdaDataManager :: get_instance()->get_alias(CdaLanguage :: get_table_name());
		$usr_alias = UserDataManager :: get_instance()->get_database()->get_alias(User :: get_table_name());
		
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $usr_alias);
		$columns[] = new ObjectTableColumn(CdaLanguage :: PROPERTY_ENGLISH_NAME, true, $alias);
		$columns[] = new ObjectTableColumn(CdaLanguage :: PROPERTY_ENGLISH_NAME, true, $alias . '2');
		$columns[] = new ObjectTableColumn(TranslatorApplication :: PROPERTY_DATE);
		$columns[] = new ObjectTableColumn(TranslatorApplication :: PROPERTY_STATUS);

		return $columns;
	}
}
?>