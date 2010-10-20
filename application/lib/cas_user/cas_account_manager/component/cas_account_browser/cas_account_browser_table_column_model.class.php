<?php
/**
 * @package cda.tables.cas_account_table
 */

require_once dirname(__FILE__) . '/../../../tables/cas_account_table/default_cas_account_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../cas_account.class.php';

/**
 * Table column model for the cas_account browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class CasAccountBrowserTableColumnModel extends DefaultCasAccountTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function CasAccountBrowserTableColumnModel()
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