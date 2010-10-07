<?php
/**
 * @package context_linker.tables.context_link_table
 */

require_once dirname(__FILE__).'/../../context_link.class.php';

/**
 * Default cell renderer for the context_link table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultContextLinkTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultContextLinkTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param ContextLink $context_link - The context_link
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $context_link)
	{
		switch ($column->get_name())
		{
			case ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID :
				return $context_link->get_original_content_object_id();
			case ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID :
				return $context_link->get_alternative_content_object_id();
			case ContextLink :: PROPERTY_METADATA_PROPERTY_VALUE_ID :
				return $context_link->get_metadata_property_value_id();
			case ContextLink :: PROPERTY_DATE :
				return $context_link->get_date();
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