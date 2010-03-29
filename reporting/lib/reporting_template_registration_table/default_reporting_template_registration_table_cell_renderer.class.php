<?php
/**
 * $Id: default_reporting_template_registration_table_cell_renderer.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_template_registration_table
 * @author Michael Kyndt
 */

/**
 * TODO: Add comment
 */
class DefaultReportingTemplateRegistrationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultReportingTemplateRegistrationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $reporting_template_registration)
    {
        switch ($column->get_name())
        {
            case ReportingTemplateRegistration :: PROPERTY_APPLICATION :
                return Translation :: get(Utilities :: underscores_to_camelcase($reporting_template_registration->get_application()));
            case ReportingTemplateRegistration :: PROPERTY_TEMPLATE :
                return Translation :: get(Utilities :: underscores_to_camelcase($reporting_template_registration->get_template()));
            case ReportingTemplateRegistration :: PROPERTY_PLATFORM :
                $description = strip_tags($reporting_template_registration->get_platform());
                $description = Utilities :: truncate_string($description, 50);
                return Translation :: get($description);
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>