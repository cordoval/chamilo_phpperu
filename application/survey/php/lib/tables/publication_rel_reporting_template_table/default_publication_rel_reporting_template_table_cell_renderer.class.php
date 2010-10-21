<?php 
namespace survey;

use common\libraries\ObjectTableCellRenderer;

class DefaultSurveyPublicationRelReportingTemplateTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyPublicationRelReportingTemplateTableCellRenderer()
    {
    }

    function render_cell($column, $publication_rel_reporting_template_registration)
    {
        switch ($column->get_name())
        {
            case SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_PUBLICATION_ID :
                return $publication_rel_reporting_template_registration->get_publication_id();
            case SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_REPORTING_TEMPLATE_REGISTRATION_ID :
                return $publication_rel_reporting_template_registration->get_reporting_template_registration_id();
            case ReportingTemplateRegistration :: PROPERTY_TEMPLATE :
                return $publication_rel_reporting_template_registration->get_optional_property(ReportingTemplateRegistration :: PROPERTY_TEMPLATE);
            case SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_NAME :
                return $publication_rel_reporting_template_registration->get_name();
            case SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_DESCRIPTION :
                return $publication_rel_reporting_template_registration->get_description();
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