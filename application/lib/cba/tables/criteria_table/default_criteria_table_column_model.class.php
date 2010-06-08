<?php
require_once dirname(__FILE__).'/../../criteria.class.php';

/**
 * Default column model for the criteria table
 *
 * @author Nick Van Loocke
 */
class DefaultCriteriaTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCriteriaTableColumnModel()
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

		$columns[] = new ObjectTableColumn(Criteria :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(Criteria :: PROPERTY_DESCRIPTION);

		return $columns;
	}
}
?>