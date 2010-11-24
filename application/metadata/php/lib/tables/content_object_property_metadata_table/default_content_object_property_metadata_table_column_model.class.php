<?php
namespace application\metadata;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * Default column model for the content_object_property_metadata table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class DefaultContentObjectPropertyMetadataTableColumnModel extends ObjectTableColumnModel
{
	const COLUMN_TYPE_PROPERTY_TYPE = 'property_type';
        /**
	 * Constructor
	 */
	function __construct()
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

            $columns[] = new ObjectTableColumn(self :: COLUMN_TYPE_PROPERTY_TYPE);
            $columns[] = new ObjectTableColumn(ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY);
            $columns[] = new ObjectTableColumn(ContentObjectPropertyMetadata :: PROPERTY_SOURCE);

            return $columns;
	}
}
?>