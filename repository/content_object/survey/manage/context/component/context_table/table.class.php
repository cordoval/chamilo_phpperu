<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';


class SurveyContextTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_context_table';

    
    function SurveyContextTable($component, $parameters, $condition, $context_registration)
    {
        $context_type = $context_registration->get_type();
    	$model = new SurveyContextTableColumnModel($context_type);
        $renderer = new SurveyContextTableCellRenderer($component, $context_registration->get_id());
        $data_provider = new SurveyContextTableDataProvider($component, $condition, $context_type);
        parent :: __construct($data_provider, SurveyContextTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
             
        $actions = new ObjectTableFormActions(SurveyContextManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_DELETE_CONTEXT, Translation :: get('Delete')));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(SurveyContextManager :: PARAM_CONTEXT_ID, $ids);
    }
}
?>