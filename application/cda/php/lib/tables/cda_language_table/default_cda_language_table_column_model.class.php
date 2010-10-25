<?php

namespace application\cda;

use common\libraries\ObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;
use common\libraries\ObjectTableColumn;

/**
 * @package cda.tables.cda_language_table
 */
/**
 * Default column model for the cda_language table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
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

		$columns[] = new ObjectTableColumn(CdaLanguage :: PROPERTY_ORIGINAL_NAME);
		$columns[] = new ObjectTableColumn(CdaLanguage :: PROPERTY_ENGLISH_NAME);
		$columns[] = new ObjectTableColumn(CdaLanguage :: PROPERTY_RTL);
		$columns[] = new StaticTableColumn('Status');
		$columns[] = new StaticTableColumn('TranslationProgress');
		return $columns;
	}
}
?>