<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\extensions\external_repository_manager\DefaultExternalRepositoryObjectTableColumnModel;
use common\libraries\StaticTableColumn;
/**
 * $Id: repository_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
/**
 * Table column model for the repository browser table
 */
class YoutubeExternalRepositoryTableColumnModel extends DefaultExternalRepositoryObjectTableColumnModel
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
//        $this->add_column(new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE));
//        $this->add_column(new StaticTableColumn(Translation :: get('Versions')));
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