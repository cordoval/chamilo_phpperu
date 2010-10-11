<?php


/**
 * TODO: Add comment
 */
class DefaultSurveyReportingTemplateTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSurveyReportingTemplateTableColumnModel()
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
        return $columns;
    }
}
?>