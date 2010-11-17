<?php namespace application\survey;

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

use common\libraries\ObjectTable;

class SurveyPublicationRelReportingTemplateTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_publication_rel_reporting_template_table';

    function SurveyPublicationRelReportingTemplateTable($browser, $parameters, $condition)
    {
        $model = new SurveyPublicationRelReportingTemplateTableColumnModel();
        $renderer = new SurveyPublicationRelReportingTemplateTableCellRenderer($browser);
        $data_provider = new SurveyPublicationRelReportingTemplateTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyPublicationRelReportingTemplateTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
        
        $actions = new ObjectTableFormActions(SurveyReportingManager :: PARAM_ACTION);
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ACTIVATE, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        {
            $actions->add_form_action(new ObjectTableFormAction(SurveyReportingManager :: ACTION_DELETE, Translation :: get('Delete')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(SurveyReportingManager :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID, $ids);
    }
}
?>