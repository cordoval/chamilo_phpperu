<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;

/**
 * $Id: default_shared_content_objects_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
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
class DefaultSharedContentObjectsTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSharedContentObjectsTableColumnModel()
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
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE);
        $columns[] = new StaticTableColumn(Translation :: get('Versions'));
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_OWNER_ID);
        return $columns;
    }
}
?>