<?php

namespace application\package;

use common\libraries\ObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;
use common\libraries\ObjectTableColumn;

/**
 * @package package.tables.package_language_table
 */
/**
 * Default column model for the package_language table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultPackageTableColumnModel extends ObjectTableColumnModel
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
		$columns[] = new ObjectTableColumn(Package :: PROPERTY_SECTION);
		$columns[] = new ObjectTableColumn(Package :: PROPERTY_NAME);
		$columns[] = new ObjectTableColumn(Package :: PROPERTY_VERSION);
		$columns[] = new ObjectTableColumn(Package :: PROPERTY_CYCLE_PHASE);
		$columns[] = new ObjectTableColumn(Package :: PROPERTY_DESCRIPTION);
		$columns[] = new ObjectTableColumn(Package :: PROPERTY_STATUS);

		return $columns;
	}
}
?>