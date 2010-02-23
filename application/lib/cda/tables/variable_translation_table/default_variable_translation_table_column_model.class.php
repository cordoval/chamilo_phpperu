<?php
/**
 * @package cda.tables.variable_translation_table
 */
require_once dirname(__FILE__).'/../../variable_translation.class.php';

/**
 * Default column model for the variable_translation table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultVariableTranslationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultVariableTranslationTableColumnModel()
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

		$columns[] = new ObjectTableColumn(Variable :: PROPERTY_VARIABLE, true, CdaDataManager :: get_instance()->get_alias(Variable :: get_table_name()));

		$language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
		$can_translate = CdaRights :: is_allowed(CdaRights :: VIEW_RIGHT, $language_id, 'cda_language');
		$can_lock = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, $language_id, 'cda_language');

		$source_id = LocalSetting :: get('source_language', CdaManager :: APPLICATION_NAME);
		$english_id = CdaDataManager :: get_instance()->retrieve_cda_language_english()->get_id();

		if ($english_id != $language_id)
		{
			$columns[] = new StaticTableColumn(Translation :: get('EnglishTranslation'));
		}

		if(($can_translate || $can_lock) && $source_id != $english_id)
		{
			$columns[] = new StaticTableColumn(Translation :: get('SourceTranslation'));
		}

		$columns[] = new ObjectTableColumn(VariableTranslation :: PROPERTY_TRANSLATION);
		$columns[] = new StaticTableColumn(Translation :: get('Status'));
		$columns[] = new ObjectTableColumn(VariableTranslation :: PROPERTY_RATING);

		return $columns;
	}
}
?>