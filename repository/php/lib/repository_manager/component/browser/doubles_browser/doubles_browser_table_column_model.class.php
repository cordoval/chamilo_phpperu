<?php
namespace repository;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;

/**
 * $Id: doubles_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.doubles_browser
 */

require_once dirname(__FILE__) . '/../../../../content_object_table/default_content_object_table_column_model.class.php';

/**
 * Table column model for the repository browser table
 */
class DoublesBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct($is_detail)
    {
        parent :: __construct();
        $this->set_default_order_column(0);

        if(!$is_detail)
        {
        	$this->add_column(new ObjectTableColumn('Duplicates'), false);
        }

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