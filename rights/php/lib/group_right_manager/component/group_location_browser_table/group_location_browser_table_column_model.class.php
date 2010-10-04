<?php
/**
 * $Id: group_location_browser_table_column_model.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager.component.group_location_browser_table
 */
require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class GroupLocationBrowserTableColumnModel extends DefaultLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;
    private static $rights_columns;
    private $browser;

    /**
     * Constructor
     */
    function GroupLocationBrowserTableColumnModel($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->add_rights_columns();
        //        $this->add_column(self :: get_modification_column());
        $this->set_default_order_column(1);
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

    static function is_rights_column($column)
    {
        return in_array($column, self :: $rights_columns);
    }

    function add_rights_columns()
    {
        $rights = RightsUtilities :: get_available_rights($this->browser->get_source());
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Utilities :: underscores_to_camelcase(strtolower($right_name));
            $column = new StaticTableColumn(Translation :: get($column_name));
            $this->add_column($column);
            self :: $rights_columns[] = $column;
        }
    }
}
?>