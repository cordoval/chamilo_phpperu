<?php

namespace application\package;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * @package package.tables.language_pack_table
 */
/**
 * Default column model for the language_pack table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultLanguagePackTableColumnModel extends ObjectTableColumnModel
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

		$columns[] = new ObjectTableColumn(LanguagePack :: PROPERTY_BRANCH);
		$columns[] = new ObjectTableColumn(LanguagePack :: PROPERTY_NAME);
		$columns[] = new ObjectTableColumn(LanguagePack :: PROPERTY_TYPE);

		return $columns;
	}
}
?>