<?php
/**
 * @package context_linker.tables.context_link_table
 */
require_once dirname(__FILE__).'/../../context_link.class.php';

/**
 * Default column model for the context_link table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultContextLinkTableColumnModel extends ObjectTableColumnModel
{
    const COLUMN_TYPE = 'type';
    const COLUMN_TITLE = 'title';
    const COLUMN_METADATA_PROPERTY_TYPE = 'metadata_property_type';
    const COLUMN_METADATA_PROPERTY_VALUE = 'metadata_property_value';
    /**
	 * Constructor
	 */
	function DefaultContextLinkTableColumnModel()
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

		//$columns[] = new ObjectTableColumn(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID);
                $columns[] = new ObjectTableColumn(self :: COLUMN_TYPE);
                $columns[] = new ObjectTableColumn(self :: COLUMN_TITLE);
                $columns[] = new ObjectTableColumn(self :: COLUMN_METADATA_PROPERTY_TYPE);
		$columns[] = new ObjectTableColumn(self :: COLUMN_METADATA_PROPERTY_VALUE);
		
                return $columns;
	}
}
?>