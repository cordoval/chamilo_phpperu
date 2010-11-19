<?php namespace repository\content_object\survey;

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';


class SurveyTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_context_table';


    function __construct($component, $parameters, $condition)
    {
       	$model = new SurveyTableColumnModel($context_type);
        $renderer = new SurveyTableCellRenderer($component);
        $data_provider = new SurveyTableDataProvider($component, $condition);
        parent :: __construct($data_provider, SurveyTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        $actions = new ObjectTableFormActions(__NAMESPACE__, SurveyContextManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_DELETE_SURVEY_REL_CONTEXT_TEMPLATE, Translation :: get('Delete', null, Utilities::COMMON_LIBRARIES)));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(SurveyContextManager :: PARAM_SURVEY_ID, $ids);
    }
}
?>