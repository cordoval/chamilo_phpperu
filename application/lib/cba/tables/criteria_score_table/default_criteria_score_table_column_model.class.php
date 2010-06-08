<?php
require_once dirname(__FILE__).'/../../criteria_score.class.php';

/**
 * Default column model for the criteria score table
 *
 * @author Nick Van Loocke
 */
class DefaultCriteriaScoreTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCriteriaScoreTableColumnModel()
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

		$columns[] = new ObjectTableColumn(CriteriaScore :: PROPERTY_CRITERIA_ID);
		$columns[] = new ObjectTableColumn(CriteriaScore :: PROPERTY_DESCRIPTION_SCORE);
		$columns[] = new ObjectTableColumn(CriteriaScore :: PROPERTY_SCORE);

		return $columns;
	}
}
?>