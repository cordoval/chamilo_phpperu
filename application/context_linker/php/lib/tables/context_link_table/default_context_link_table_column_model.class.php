<?php
namespace application\context_linker;
use common\libraries\ObjectTableColumnModel;

/**
 * Default column model for the context_link table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultContextLinkTableColumnModel extends ObjectTableColumnModel
{
    const COLUMN_ORIGINAL_TYPE = 'original_type';
    const COLUMN_ORIGINAL_TITLE = 'original_title';
    const COLUMN_ALTERNATIVE_TYPE = 'alternative_type';
    const COLUMN_ALTERNATIVE_TITLE = 'alternative_title';
    const COLUMN_METADATA_PROPERTY_TYPE = 'metadata_property_type';
    const COLUMN_METADATA_PROPERTY_VALUE = 'metadata_property_value';
    const COLUMN_DATE = 'date';
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
                $columns[] = new ObjectTableColumn(self :: COLUMN_ORIGINAL_TYPE);
                $columns[] = new ObjectTableColumn(self :: COLUMN_ORIGINAL_TITLE);
                $columns[] = new ObjectTableColumn(self :: COLUMN_ALTERNATIVE_TYPE);
                $columns[] = new ObjectTableColumn(self :: COLUMN_ALTERNATIVE_TITLE);
                $columns[] = new ObjectTableColumn(self :: COLUMN_METADATA_PROPERTY_TYPE);
		$columns[] = new ObjectTableColumn(self :: COLUMN_METADATA_PROPERTY_VALUE);
                $columns[] = new ObjectTableColumn(self :: COLUMN_DATE);
		
                return $columns;
	}
}
?>