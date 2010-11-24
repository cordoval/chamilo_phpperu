<?php

namespace application\distribute;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
/**
 * $Id: announcement_distribution_browser_table.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.distribute_manager.component.announcement_distribution_browser
 */
require_once WebApplication :: get_application_class_lib_path('distribute') . 'distribute_manager/component/announcement_distribution_browser/announcement_distribution_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('distribute') . 'distribute_manager/component/announcement_distribution_browser/announcement_distribution_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('distribute') . 'distribute_manager/component/announcement_distribution_browser/announcement_distribution_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of announcement distributions.
 */
class AnnouncementDistributionBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'announcement_distribution_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $name, $parameters, $condition)
    {
        $model = new AnnouncementDistributionBrowserTableColumnModel();
        $renderer = new AnnouncementDistributionBrowserTableCellRenderer($browser);
        $data_provider = new AnnouncementDistributionBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, AnnouncementDistributionBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        //$actions[DistributeManager :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>