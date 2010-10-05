<?php
/**
 * $Id: default_laika_attempt_table_column_model.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.tables.laika_attempt_table
 */
require_once dirname(__FILE__) . '/../../laika_attempt.class.php';

/**
 * TODO: Add comment
 */
class DefaultLaikaAttemptTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultLaikaAttemptTableColumnModel()
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