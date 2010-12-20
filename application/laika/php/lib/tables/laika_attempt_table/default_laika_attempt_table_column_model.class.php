<?php
namespace application\laika;

use common\libraries\ObjectTableColumn;
use common\libraries\ObjectTableColumnModel;
/**
 * $Id: default_laika_attempt_table_column_model.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.tables.laika_attempt_table
 */
/**
 * TODO: Add comment
 */
class DefaultLaikaAttemptTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct(self :: get_default_columns(), 3);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(LaikaAttempt :: PROPERTY_DATE);
        return $columns;
    }
}
?>