<?php
require_once dirname(__FILE__).'/../../competency.class.php';

/**
 * Default column model for the competency table
 *
 * @author Nick Van Loocke
 */
class DefaultCompetencyTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCompetencyTableColumnModel()
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

		$columns[] = new ObjectTableColumn(Competency :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(Competency :: PROPERTY_DESCRIPTION);

		return $columns;
	}
}
?>