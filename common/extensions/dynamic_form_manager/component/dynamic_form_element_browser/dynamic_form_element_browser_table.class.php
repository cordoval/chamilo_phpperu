<?php
/**
 * $Id: dynamic_form_element_browser_table.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component.dynamic_form_element_browser
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/dynamic_form_element_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/dynamic_form_element_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/dynamic_form_element_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of users.
 */
class DynamicFormElementBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'dynamic_form_element_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function DynamicFormElementBrowserTable($browser, $parameters, $condition)
    {
        $model = new DynamicFormElementBrowserTableColumnModel();
        $renderer = new DynamicFormElementBrowserTableCellRenderer($browser);
        $data_provider = new DynamicFormElementBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, DynamicFormElementBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $actions[] =  new ObjectTableFormAction(DynamicFormManager :: PARAM_DELETE_FORM_ELEMENETS, Translation :: get('RemoveSelected'));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>