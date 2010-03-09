<?php
require_once dirname(__FILE__).'/../../criteria.class.php';

/**
 * Default cell renderer for the criteria table
 *
 * @author Nick Van Loocke
 */
class DefaultCriteriaTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCriteriaTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Competency $criteria - The criteria
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $criteria)
	{
		switch ($column->get_name())
		{
			case Criteria :: PROPERTY_ID :
				return $criteria->get_id();
			case Criteria :: PROPERTY_TITLE :
				return $criteria->get_title();
			case Criteria :: PROPERTY_DESCRIPTION :
				return $criteria->get_description();
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