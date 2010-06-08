<?php
/**
 * $Id: location_group_browser_table_column_model.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component.location_group_bowser
 */
require_once Path :: get_group_path() . 'lib/group_table/default_group_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class LocationGroupBrowserTableColumnModel extends DefaultGroupTableColumnModel
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
    function LocationGroupBrowserTableColumnModel($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->set_default_order_column(1);
        $this->set_columns(array_slice($this->get_columns(), 0, 1));
        $this->add_column(new StaticTableColumn(Translation :: get('Users')));
        $this->add_column(new StaticTableColumn(Translation :: get('Subgroups')));
        $this->add_rights_columns();
        //		$this->add_column(self :: get_modification_column());
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
        $rights = $this->browser->get_available_rights();
        
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