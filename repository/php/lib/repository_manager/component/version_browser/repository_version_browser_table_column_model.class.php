<?php
namespace repository;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;

use user\UserManager;

/**
 * $Id: repository_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/../../../content_object_table/default_content_object_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class RepositoryVersionBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
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
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE, false);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, false);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, false);
        $columns[] = new StaticTableColumn(Translation :: get('User', null, UserManager :: APPLICATION_NAME));
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE, false);
        $columns[] = self :: get_modification_column();

        ObjectTableColumnModel :: __construct($columns);
        $this->set_default_order_column(4);
        $this->set_default_order_direction(SORT_DESC);
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