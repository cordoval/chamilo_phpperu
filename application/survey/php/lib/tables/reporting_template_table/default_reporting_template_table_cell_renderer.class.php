<?php 
namespace survey;

use common\libraries\ObjectTableCellRenderer;

class DefaultSurveyReportingTemplateTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyReportingTemplateTableCellRenderer()
    {
    }

    function render_cell($column, $reporting_template_registration)
    {
        switch ($column->get_name())
        {
            case ReportingTemplateRegistration :: PROPERTY_APPLICATION :
                return $reporting_template_registration->get_application();
            case ReportingTemplateRegistration :: PROPERTY_TEMPLATE :
                return $reporting_template_registration->get_template();
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