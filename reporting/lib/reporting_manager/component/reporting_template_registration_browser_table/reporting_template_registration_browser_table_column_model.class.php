<?php
/**
 * $Id: reporting_template_registration_browser_table_column_model.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component.reporting_template_registration_browser_table
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path() . 'lib/reporting_template_registration_table/default_reporting_template_registration_table_column_model.class.php';
/**
 * Table column model for the reporting browser table
 */
class ReportingTemplateRegistrationBrowserTableColumnModel extends DefaultReportingTemplateRegistrationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function ReportingTemplateRegistrationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ReportingTemplateRegistrationTableColumn
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