<?php
/**
 * $Id: laika_attempt_browser_table_column_model.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_attempt_browser
 */
require_once dirname(__FILE__) . '/../../../tables/laika_attempt_table/default_laika_attempt_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../laika_attempt.class.php';
/**
 * Table column model for the user browser table
 */
class LaikaAttemptBrowserTableColumnModel extends DefaultLaikaAttemptTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function LaikaAttemptBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
