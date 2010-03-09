<?php
require_once dirname(__FILE__).'/../../indicator.class.php';

/**
 * Default column model for the indicator table
 *
 * @author Nick Van Loocke
 */
class DefaultIndicatorTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultIndicatorTableColumnModel()
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

		$columns[] = new ObjectTableColumn(Indicator :: PROPERTY_ID);
		$columns[] = new ObjectTableColumn(Indicator :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(Indicator :: PROPERTY_DESCRIPTION);

		return $columns;
	}
}
?>