<?php
require_once dirname(__FILE__).'/../../criteria_score.class.php';

/**
 * Default cell renderer for the criteria score table
 *
 * @author Nick Van Loocke
 */
class DefaultCriteriaScoreTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCriteriaScoreTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Competency $criteria - The criteria
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $criteria_score)
	{
		switch ($column->get_name())
		{
			case CriteriaScore :: PROPERTY_ID :
				return $criteria_score->get_id();
			case CriteriaScore :: PROPERTY_CRITERIA_ID :
				return $criteria_score->get_criteria_id();
			case CriteriaScore :: PROPERTY_DESCRIPTION_SCORE :
				return $criteria_score->get_description_score();
			case CriteriaScore :: PROPERTY_SCORE :
				return $criteria_score->get_score();
			default :
				return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>