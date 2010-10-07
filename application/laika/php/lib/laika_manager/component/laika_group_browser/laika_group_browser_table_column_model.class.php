<?php
/**
 * $Id: laika_group_browser_table_column_model.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_group_browser
 */
require_once CoreApplication :: get_application_class_lib_path('group') . 'group_table/default_group_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class LaikaGroupBrowserTableColumnModel extends DefaultGroupTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function LaikaGroupBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(new StaticTableColumn(Translation :: get('Subgroups')));
        $this->add_column(new StaticTableColumn(Translation :: get('Users')));
        $this->add_column(new StaticTableColumn(Translation :: get('Participants')));
        $this->add_column(new StaticTableColumn(Translation :: get('Percentage')));
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