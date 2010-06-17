<?php
require_once dirname(__FILE__).'/../../indicator.class.php';

/**
 * Default cell renderer for the indicator table
 *
 * @author Nick Van Loocke
 */
class DefaultIndicatorTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultIndicatorTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Indicator $indicator - The indicator
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $indicator)
	{
		switch ($column->get_name())
		{
			case Indicator :: PROPERTY_ID :
				return $indicator->get_id();
			case Indicator :: PROPERTY_TITLE :
				return $indicator->get_title();
			case Indicator :: PROPERTY_DESCRIPTION :
				return $indicator->get_description();
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