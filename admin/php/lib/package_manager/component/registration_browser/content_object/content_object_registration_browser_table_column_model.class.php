<?php
namespace admin;

use common\libraries\ObjectTableColumn;
use common\libraries\Path;
use common\libraries\StaticTableColumn;
/**
 * $Id: content_object_registration_browser_table_column_model.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component.registration_browser
 */
require_once Path :: get_admin_path() . 'lib/tables/registration_table/default_registration_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class ContentObjectRegistrationBrowserTableColumnModel extends DefaultRegistrationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(new ObjectTableColumn(Registration :: PROPERTY_CATEGORY));
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