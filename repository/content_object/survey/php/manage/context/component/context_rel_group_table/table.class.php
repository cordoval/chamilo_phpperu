<?php namespace repository\content_object\survey;

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyContextRelGroupTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_context_rel_group_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {

        $model = new SurveyContextRelGroupTableColumnModel($browser);
        $renderer = new SurveyContextRelGroupTableCellRenderer($browser);
        $data_provider = new SurveyContextRelGroupTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextRelGroupTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(__NAMESPACE__, SurveyContextManager :: PARAM_ACTION);
//        if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_GROUP_RIGHT, $browser->get_period()->get_id(), SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
//        {
            $actions->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_UNSUBSCRIBE_GROUP, Translation :: get('Unsubscribe')));
//        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(SurveyContextManager :: PARAM_CONTEXT_REL_GROUP_ID, $ids);
    }
}
?>