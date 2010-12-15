<?php

namespace application\package;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\Request;
use common\libraries\LocalSetting;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;
/**
 * @package package.tables.variable_translation_table
 */
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
	function __construct()
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

		$columns[] = new ObjectTableColumn(Variable :: PROPERTY_VARIABLE, true, PackageDataManager :: get_instance()->get_alias(Variable :: get_table_name()));

		$language_id = Request :: get(PackageManager :: PARAM_PACKAGE_LANGUAGE);
		$can_translate = PackageRights :: is_allowed(PackageRights :: VIEW_RIGHT, $language_id, 'package_language');
		$can_lock = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, $language_id, 'package_language');

		$source_id = LocalSetting :: get('source_language', PackageManager :: APPLICATION_NAME);
		$english_id = PackageDataManager :: get_instance()->retrieve_package_language_english()->get_id();

		if ($english_id != $language_id)
		{
			$columns[] = new StaticTableColumn('EnglishTranslation');
		}

		if(($can_translate || $can_lock) && $source_id != $english_id)
		{
			$columns[] = new StaticTableColumn('SourceTranslation');
		}

		$columns[] = new ObjectTableColumn(VariableTranslation :: PROPERTY_TRANSLATION);
		$columns[] = new StaticTableColumn('Status');
		$columns[] = new ObjectTableColumn(VariableTranslation :: PROPERTY_RATING);

		return $columns;
	}
}
?>