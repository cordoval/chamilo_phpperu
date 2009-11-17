<?php
/**
 * $Id: reporting_template_registration_browser_table.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component.reporting_template_registration_browser_table
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template_registration_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/reporting_template_registration_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/reporting_template_registration_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of reporting templates
 */
class ReportingTemplateRegistrationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'reporting_template_registration_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ReportingTemplateRegistrationBrowserTable($browser, $parameters, $condition)
    {
        $model = new ReportingTemplateRegistrationBrowserTableColumnModel();
        $renderer = new ReportingTemplateRegistrationBrowserTableCellRenderer($browser);
        $data_provider = new ReportingTemplateRegistrationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, ReportingTemplateRegistrationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

    function get_objects($offset, $count, $order_column)
    {
        $reporting_template_registrations = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        while ($reporting_template_registration = $reporting_template_registrations->next_result())
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $reporting_template_registration->get_name();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $reporting_template_registration);
            }
            $table_data[] = $row;
        }
        return $table_data;
    }
}
?>