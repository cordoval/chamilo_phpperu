<?php
namespace repository;

use common\libraries\ComplexMenuSupport;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableColumn;

/**
 * $Id: complex_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.complex_browser
 */
/**
 * Table column model for the repository browser table
 */
class ComplexBrowserTableColumnModel extends ObjectTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct($browser, $additional_columns = array())
    {
        parent :: __construct(self :: get_default_columns($browser, $additional_columns), 1);
        $this->set_default_order_column(0);
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

    private static function get_default_columns($browser, $additional_columns = array())
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE, false);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, false);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, false);

        if ($browser instanceof ComplexMenuSupport)
        {
            $columns[] = new ObjectTableColumn('subitems', false);
        }

        foreach ($additional_columns as $additional_column)
        {
            $columns[] = $additional_column;
        }

        $columns[] = self :: get_modification_column();
        return $columns;
    }
}
?>