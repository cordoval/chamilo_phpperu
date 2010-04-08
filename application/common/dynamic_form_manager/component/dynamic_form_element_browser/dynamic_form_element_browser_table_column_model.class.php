<?php
/**
 * $Id: dynamic_form_element_browser_table_column_model.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component.dynamic_form_element_browser
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../dynamic_form_element_table/default_dynamic_form_element_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class DynamicFormElementBrowserTableColumnModel extends DefaultDynamicFormElementTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function DynamicFormElementBrowserTableColumnModel()
    {
        parent :: __construct();
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