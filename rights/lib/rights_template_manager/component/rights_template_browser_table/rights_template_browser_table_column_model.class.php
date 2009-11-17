<?php
/**
 * $Id: rights_template_browser_table_column_model.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component.rights_template_browser_table
 */
require_once dirname(__FILE__) . '/../../../tables/rights_template_table/default_rights_template_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class RightsTemplateBrowserTableColumnModel extends DefaultRightsTemplateTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function RightsTemplateBrowserTableColumnModel()
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
