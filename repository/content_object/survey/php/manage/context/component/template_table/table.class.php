<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';


class SurveyTemplateTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_template_table';

    
    function SurveyTemplateTable($component, $parameters, $condition, $context_template)
    {
        $template_type = $context_template->get_type();
    	$model = new SurveyTemplateTableColumnModel($template_type);
        $renderer = new SurveyTemplateTableCellRenderer($component, $context_template->get_id());
        $data_provider = new SurveyTemplateTableDataProvider($component, $condition, $template_type);
        parent :: __construct($data_provider, SurveyTemplateTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
             
        $actions = new ObjectTableFormActions(SurveyContextManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_DELETE_CONTEXT_TEMPLATE, Translation :: get('Delete')));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID, $ids);
    }
}
?>