<?php
/**
 * $Id: default_content_object_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';

/**
 * This is the default column model, used when a ContentObjectTable does not
 * provide its own model.
 *
 * The default model contains the following columns:
 *
 * - The type of the learning object
 * - The title of the learning object
 * - The description of the learning object
 * - The date when the learning object was last modified
 *
 * Although this model works best in conjunction with the default cell
 * renderer, it can be used with any ContentObjectTableCellRenderer.
 *
 * @see ContentObjectTable
 * @see ContentObjectTableColumnModel
 * @see DefaultContentObjectTableCellRenderer
 * @author Tim De Pauw
 */
class DefaultContentObjectTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultContentObjectTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
        /*$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE);
		$columns[] = new StaticTableColumn(Translation :: get('Versions'));*/
        return $columns;
    }

    function get_display_order_column_property()
    {
        return ContentObject :: PROPERTY_DISPLAY_ORDER_INDEX;
    }
}
?>