<?php 
namespace application\survey;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

/**
 * TODO: Add comment
 */
class DefaultSurveyPublicationRelReportingTemplateTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSurveyPublicationRelReportingTemplateTableColumnModel()
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
        
        $columns[] = new ObjectTableColumn(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_DESCRIPTION);
        //        $columns[] = new ObjectTableColumn(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_PUBLICATION_ID);
        //        $columns[] = new ObjectTableColumn(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_REPORTING_TEMPLATE_REGISTRATION_ID);
        //        $columns[] = new ObjectTableColumn(ReportingTemplateRegistration :: PROPERTY_TEMPLATE);
        

        return $columns;
    }
}
?>