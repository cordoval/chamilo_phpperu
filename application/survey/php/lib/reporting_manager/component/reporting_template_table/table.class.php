<?php namespace application\survey;

use common\libraries\ObjectTable;

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyReportingTemplateTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_reporting_template_table';

    function SurveyReportingTemplateTable($browser, $parameters, $condition)
    {
        $model = new SurveyReportingTemplateTableColumnModel();
        $renderer = new SurveyReportingTemplateTableCellRenderer($browser);
        $data_provider = new SurveyReportingTemplateTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyReportingTemplateTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);

        $actions = new ObjectTableFormActions(__NAMESPACE__, SurveyReportingManager :: PARAM_ACTION);
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ACTIVATE, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        {
            $actions->add_form_action(new ObjectTableFormAction(SurveyReportingManager :: ACTION_CREATE, Translation :: get('Activate')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(SurveyReportingManager :: PARAM_REPORTING_TEMPLATE_REGISTRATION_ID, $ids);
    }
}
?>