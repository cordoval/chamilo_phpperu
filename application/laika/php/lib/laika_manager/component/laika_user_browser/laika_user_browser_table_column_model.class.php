<?php
namespace application\laika;

use common\libraries\Application;
use common\libraries\CoreApplication;
use common\libraries\StaticTableColumn;

use user\DefaultUserTableColumnModel;

/**
 * $Id: laika_user_browser_table_column_model.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_user_browser
 */
require_once CoreApplication :: get_application_class_lib_path('user') . 'user_table/default_user_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class LaikaUserBrowserTableColumnModel extends DefaultUserTableColumnModel
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