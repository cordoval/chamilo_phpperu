<?php
/**
 * $Id: default_reporting_template_registration_table_column_model.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_template_registration_table
 * @author Michael Kyndt
 */

/**
 * TODO: Add comment
 */
class DefaultReportingTemplateRegistrationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultReportingTemplateRegistrationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(ReportingTemplateRegistration :: PROPERTY_APPLICATION);
        $columns[] = new ObjectTableColumn(ReportingTemplateRegistration :: PROPERTY_TEMPLATE);
        $columns[] = new ObjectTableColumn(ReportingTemplateRegistration :: PROPERTY_PLATFORM);
        return $columns;
    }
}
?>