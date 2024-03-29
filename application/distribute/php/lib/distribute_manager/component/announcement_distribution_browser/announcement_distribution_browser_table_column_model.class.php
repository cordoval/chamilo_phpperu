<?php

namespace application\distribute;

use common\libraries\WebApplication;
use common\libraries\StaticTableColumn;
/**
 * $Id: announcement_distribution_browser_table_column_model.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.distribute_manager.component.announcement_distribution_browser
 */
require_once WebApplication :: get_application_class_lib_path('distribute') . 'tables/announcement_distribution_table/default_announcement_distribution_table_column_model.class.php';
/**
 * Table column model for the announcement distribution browser table
 */
class AnnouncementDistributionBrowserTableColumnModel extends DefaultAnnouncementDistributionTableColumnModel
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
        $this->set_default_order_column(3);
        $this->set_default_order_direction(SORT_DESC);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return AnnouncementDistributionTableColumn
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