<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyReportingTemplateTable extends ObjectTable
{
    const DEFAULT_NAME = 'reporting_template_browser_table';

    function SurveyReportingTemplateTable($browser, $parameters, $condition)
    {
        $model = new SurveyReportingTemplateTableColumnModel();
        $renderer = new SurveyReportingTemplateTableCellRenderer($browser);
        $data_provider = new SurveyReportingTemplateTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyReportingTemplateTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
        
        $actions = new ObjectTableFormActions(SurveyReportingManager :: PARAM_ACTION);
        if (SurveyRights :: is_allowed_in_internship_organizers_subtree(SurveyRights :: RIGHT_ACTIVATE, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_COMPONENT))
        {
            $actions->add_form_action(new ObjectTableFormAction(SurveyReportingManager :: ACTION_ACTIVATE_REPORTING_TEMPLATE, Translation :: get('Activate')));
        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(SurveyReportingManager :: PARAM_REGION_ID, $ids);
    }
}
?>