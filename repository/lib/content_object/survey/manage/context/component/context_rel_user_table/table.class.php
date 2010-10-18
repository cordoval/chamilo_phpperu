<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyContextRelUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_context_rel_user_browser_table';

    /**
     * Constructor
     */
    function SurveyContextRelUserBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new SurveyContextRelUserBrowserTableColumnModel($browser);
        $renderer = new SurveyContextRelUserBrowserTableCellRenderer($browser);
        $data_provider = new SurveyContextRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = new ObjectTableFormActions(SurveyContextManager :: PARAM_ACTION);
        
//        if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_AGREEMENT_USER_RIGHT, $browser->get_context()->get_id(), SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
//        {
            $actions->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_UNSUBSCRIBE_USER, Translation :: get('Unsubscribe')));
//        }
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    
    }

    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(SurveyContextManager :: PARAM_CONTEXT_REL_USER_ID, $ids);
    }
}
?>