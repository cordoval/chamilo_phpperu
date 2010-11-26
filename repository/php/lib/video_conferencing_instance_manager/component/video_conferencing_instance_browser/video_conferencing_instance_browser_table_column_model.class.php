<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;

/**
 * $Id: video_conferencing_instance_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/../../video_conferencing_instance_table/default_video_conferencing_instance_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class VideoConferencingInstanceBrowserTableColumnModel extends DefaultVideoConferencingInstanceTableColumnModel
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