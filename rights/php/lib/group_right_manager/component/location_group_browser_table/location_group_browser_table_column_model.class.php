<?php
namespace rights;

use common\libraries\Utilities;
use common\libraries\Path;

use rights\RightsUtilities;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;
use user\UserManager;
use group\GroupManager;
use group\DefaultGroupTableColumnModel;
/**
 * $Id: location_group_browser_table_column_model.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager.component.location_group_browser_table
 */
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
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->set_default_order_column(1);
        $this->set_columns(array_slice($this->get_columns(), 0, 1));
        $this->add_column(new StaticTableColumn(Translation :: get('Users', null, UserManager :: APPLICATION_NAME)));
        $this->add_column(new StaticTableColumn(Translation :: get('Subgroups', null, GroupManager :: APPLICATION_NAME)));
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