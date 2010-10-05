<?php
/**
 * @package metadata.tables.content_object_property_metadata_table
 */
require_once dirname(__FILE__).'/../../content_object_property_metadata.class.php';

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
	function DefaultContentObjectPropertyMetadataTableColumnModel()
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

            $columns[] = new ObjectTableColumn(ContentObjectPropertyMetadata :: PROPERTY_PROPERTY_TYPE_ID);
            $columns[] = new ObjectTableColumn(ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY);
            $columns[] = new ObjectTableColumn(ContentObjectPropertyMetadata :: PROPERTY_SOURCE);

            return $columns;
	}
}
?>