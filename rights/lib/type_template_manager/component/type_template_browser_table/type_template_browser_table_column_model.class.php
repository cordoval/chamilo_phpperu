<?php
/**
 * $Id: type_template_browser_table_column_model.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.type_template_manager.component.type_template_browser_table
 */
require_once dirname(__FILE__) . '/../../../tables/type_template_table/default_type_template_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class TypeTemplateBrowserTableColumnModel extends DefaultTypeTemplateTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function TypeTemplateBrowserTableColumnModel()
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