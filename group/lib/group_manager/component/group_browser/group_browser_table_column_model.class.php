<?php
/**
 * $Id: group_browser_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.group_browser
 */
require_once dirname(__FILE__) . '/../../../group_table/default_group_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class GroupBrowserTableColumnModel extends DefaultGroupTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function GroupBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(new StaticTableColumn(Translation :: get('Users')));
        $this->add_column(new StaticTableColumn(Translation :: get('Subgroups')));
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
