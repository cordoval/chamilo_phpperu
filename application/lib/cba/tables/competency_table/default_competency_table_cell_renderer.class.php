<?php
require_once dirname(__FILE__).'/../../competency.class.php';

/**
 * Default cell renderer for the competency table
 *
 * @author Nick Van Loocke
 */
class DefaultCompetencyTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCompetencyTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Competency $competency - The competency
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $competency)
	{
		switch ($column->get_name())
		{
			case Competency :: PROPERTY_ID :
				return $competency->get_id();
			case Competency :: PROPERTY_TITLE :
				return $competency->get_title();
			case Competency :: PROPERTY_DESCRIPTION :
				return $competency->get_description();
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