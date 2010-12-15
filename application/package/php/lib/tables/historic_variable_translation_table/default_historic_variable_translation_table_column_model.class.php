<?php

namespace application\package;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;


/**
 * @package package.tables.variable_translation_table
 */
/**
 * Default column model for the historic_variable_translation table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultHistoricVariableTranslationTableColumnModel extends ObjectTableColumnModel
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
		$columns[] = new ObjectTableColumn(HistoricVariableTranslation :: PROPERTY_TRANSLATION);
		$columns[] = new StaticTableColumn(Translation :: get('User', null, 'user'));
		$columns[] = new ObjectTableColumn(HistoricVariableTranslation :: PROPERTY_DATE);
		$columns[] = new StaticTableColumn(Translation :: get('Rating'));

		return $columns;
	}
}
?>