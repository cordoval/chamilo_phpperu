<?php

namespace application\gradebook;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use repository\ContentObject;

class DefaultGradebookPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent :: __construct(self :: get_default_columns());
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
		return $columns;
	}
}
?>