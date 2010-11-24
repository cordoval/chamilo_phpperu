<?php

namespace application\reservations;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;
/**
 * $Id: default_category_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.category_table
 */

/**
 * TODO: Add comment
 */
class DefaultCategoryTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
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
        $columns[] = new StaticTableColumn('', false);
        $columns[] = new ObjectTableColumn(Category :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(Category :: PROPERTY_POOL, true);
        return $columns;
    }
}
?>